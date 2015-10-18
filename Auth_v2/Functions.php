<?php

/**
 * PHP Authorization Plugin
 *
 * This plugin implements a lot of necessary functionality
 * without using any framework.
 *
 * @author  Trushin Victor <v1996-96@mail.ru>
 * @version 2.0 latest
 * @copyright (c) 2015 Trushin Victor. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl GNU GENERAL PUBLIC LICENSE v3
 */

namespace Auth_v2;

trait Functions{
	
	/**
	 * Create new token
	 * @param  int    $id_user  User id
	 * @param  bool   $remember Use cookies or session
	 */
	private function _newToken($id_user, $remember = false){
		// Create new token
		$newToken = $this->_hash( $this->_generate(30) );
		$this->_db_insertToken($newToken, $id_user);

		// Save token on user's side
		if ($remember) {
			$cookie = setcookie($this->hashName, 
								$newToken, 
								time() + $this->authTime, 
								$this->cookiePath);
			if(!$cookie)
				$_SESSION[ $this->hashName ] = $newToken;
		} else {
			$_SESSION[ $this->hashName ] = $newToken;
		}
	}


	/**
	 * Get current token
	 * @return string|bool Token
	 */
	private function _getToken(){
		if (session_status() === PHP_SESSION_ACTIVE &&
			isset($_SESSION[ $this->hashName ])){
			return $_SESSION[ $this->hashName ];

		} elseif (isset($_COOKIE[ $this->hashName ])) {
			return $_COOKIE[ $this->hashName ];

		} else {
			return false;
		}
	}


	/**
	 * Delete token
	 * @param  int|null $id Token id
	 */
	private function _destroyToken($id){
		// Delete token from user side
		session_unset();
		setcookie( $this->hashName, '', time() - 3600, $this->cookiePath );

		// Delete token from DB
		if (!is_null($id)) {
			$this->_db_deleteToken( (int)$id );
		}
	}


	/**
	 * Check current user token
	 * @param  boolean $lockscreenMethod Is this method initialized from lockscreen or not
	 * @return array                     Token information
	 */
	private function _checkCurrentToken($lockscreenMethod = false){
		// Get user's token
		if (!($token = $this->_getToken()))
			return $this->reroute($this->loginPageUrl);

		// Check token's existance
		$tokenInfo = $this->_db_getToken( $token );
		if (!($tokenInfo && count($tokenInfo) == 1)) {
			$this->_destroyToken(null);
			return $this->_setError("Wrong token", $this->loginPageUrl);
		}

		// Check token's timeout
		if ($tokenInfo[0]['diff'] > $this->authTime) {
			$this->_destroyToken( (int)$tokenInfo[0]['id'] );
			return $this->_setError("Token expired", $this->loginPageUrl);
		}

		// Check token's lockscreen timeout
		if (!$lockscreenMethod && 
			$this->useLockscreen && 
			$tokenInfo[0]['diff'] > $this->lockDelay) {
			return $this->_setError("Token expired", $this->lockscreenPageUrl);
		}

		// Verification of identity of user's ip and token's ip
		if (!$lockscreenMethod &&
			$tokenInfo[0][ $this->fTokenIp ] !== $_SERVER['REMOTE_ADDR']) {
			if ($this->checkIPToken == 'strict'){
				// If different ip is strictly prohibited
				$this->_destroyToken( (int)$tokenInfo[0]['id'] );
				return $this->_setError("Different IP", $this->loginPageUrl);

			} elseif ($this->checkIPToken == 'to_lockscreen' && $this->useLockscreen) {
				// If different ip is possible but there is a doubt in a security
				return $this->_setError("Different IP", $this->lockscreenPageUrl);
			}
		}

		return $tokenInfo;
	}


	/**
	 * Check user's IP according to white or black lists
	 * @return bool Successful or not
	 */
	private function _checkIPList(){
		$ip = $_SERVER['REMOTE_ADDR'];
		
		switch ($this->IPList) {
			case 'white':
				if (is_array($this->IPWhiteList) && 
					!in_array($ip, $this->IPWhiteList))
					return $this->_setError('Users IP is not in white list', $this->loginPageUrl);
				break;

			case 'black':
				if (is_array($this->IPBlackList) && 
					in_array($ip, $this->IPBlackList))
					return $this->_setError('Users IP is in black list', $this->loginPageUrl);
				break;
			
			default:
				return $this->_setError('Error in settings. IPList must be "white" or "black"');
		}

		return true;
	}


	/**
	 * Check current role with specified list
	 * @param  integer $role               Current user role
	 * @return boolean                     Status
	 */
	private function _checkRole($role){
		if (!$this->allowedRoles) return true;

		if (!in_array((int)$role, $this->allowedRoles)) {
			if ($this->reroute && 
				is_string($this->onRoleMismatch)) {

				return $this->_setError("User has no right to visit this page", $this->onRoleMismatch);
			} else {
				return $this->_setError("User has no right to visit this page");
			}
		} else
			return true;
	}


	/**
	 * Get hash of given string
	 * @param  string $str Input string
	 * @return string      Hash code
	 */
	private function _hash($str){
		return md5(md5($str));
	}


	/**
	 * Generate random string
	 * @param  int    $length String length
	 * @return string         Output string
	 */
	private function _generate($length){
		$chars = 'abdefhknrstyz23456789';
		$code = '';
		for ($i=0; $i < $length; $i++)
			$code .= substr($chars, rand(1, strlen($chars))-1, 1);

		return $code;
	}


	/**
	 * Switch to error mode and reroute if needed
	 * @param string $message     Error message
	 * @param string $reroutePage Page url
	 */
	private function _setError($message, $reroutePage = ""){
		if ($this->reroute && $reroutePage !== "") 
			$this->reroute( $reroutePage );

		$this->error  = true; 
		$this->status = $message;
		$this->_log($message, "Error");
		return false;
	}


	/**
	 * Makes logs in log file
	 * @param  string $message     Log message
	 * @param  string $messageType Type of log
	 */
	private function _log($message, $messageType = "Message"){
		if (!$this->makeLog) return;

		$filename = dirname(__FILE__).DIRECTORY_SEPARATOR."log.txt";
		$message = "Time:" . date("Y-m-d H:i:s") . 
				   " | Type:" . $messageType . 
				   " | IP:" . $_SERVER['REMOTE_ADDR'] .
				   " | Message:" . $message . "\r\n";
		$f = fopen($filename, "a+");
		if ($f && is_writable($filename)) {
			fwrite($f, $message);
			fclose($f);
		}
	}


	/**
	 * Get only url path without extension
	 * @param  string $str Url
	 * @return string      Output path
	 */
	private function _urlstrip($str) {
        $str = parse_url($str);
        $str = explode('.', $str["path"]);
        return $str[0];
    }


	/**
	 * Reroute to defined page
	 * @param  string $url Page url
	 */
	public function reroute($url){
		$to = $this->_urlstrip($url);
		$current = $this->_urlstrip($_SERVER['REQUEST_URI']);

		if ($this->reroute && 
			$to !== $current) {
			header("Location: " . $url); 
			die("Rerouted to " . $url);
		}
	}
	
}

?>