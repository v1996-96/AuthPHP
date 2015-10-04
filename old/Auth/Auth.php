<?php

namespace Auth;



/**
 * Authorization system
 */
class Auth extends Main
{
	//! Status variables
	private $error    = false,
			$status   = '',
			$messages = array();

	//! Database object
	private $__DB = null;

	//! Quick access to settings
	function __get($attr){
		if (array_key_exists($attr, $this->__config)) {
			return $this->__config[ $attr ];
		}
	}



	/**
	 * Connecting to a database
	 * @param  string $host  
	 * @param  string $db_name  
	 * @param  string $login 
	 * @param  string $pwd
	 */
	public function connect($host, $db_name, $login, $pwd){
		$this->__DB = new DB($host, $db_name, $login, $pwd);
	}


	/**
	 * Get current plugin error flag
	 * @return boolean 
	 */
	public function hasError(){
		return $this->error;
	}


	/**
	 * Get current plugin status
	 * @return string 
	 */
	public function getStatus(){
		return $this->status;
	}


	/**
	 * Get current plugin messages
	 * @return array 
	 */
	public function getMessages(){
		return $this->messages;
	}


	/**
	 * Login using user's data
	 * @param  string  $login    
	 * @param  string  $pwd      
	 * @param  boolean $remember 
	 * @return boolean            
	 */
	public function login($login, $pwd, $remember = false){
		session_name( $this->sessionName );
		session_start();

		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		if ($login == '' || $pwd == ''){
			$this->error  = false; 
			$this->status = "Empty fields";
			return false;
		}

		if ($this->__DBconfig['hashLogin']) $login = md5(md5($login));
		$pwd = md5(md5($pwd));

		// Check incoming data
		$userInfo = $this->__DB->getUserInfoByLP($login, $pwd);
		if (!($userInfo &&
			count($userInfo) == 1)) {
			$this->error  = true; 
			$this->status = "Wrong login or password";
			return false;
		}

		// If multiple connections are prohibited
		$tokenInfo = $this->__DB->getTokenInfoUser($userInfo[0]['id']);
		if (!$this->multiple) {
			if ($this->onMultiple == 'allow') {
				$this->__DB->deleteToken( $userInfo[0] );
			} elseif ($tokenInfo &&
					  count($tokenInfo) >= 1) {
				$this->error  = true; 
				$this->status = "User is already logged in";
				return false;
			}
		}
	
		$this->newToken( $userInfo[0]['id'], $remember );

		if ($this->reroute) {
			$this->reroute( $this->successUrl );
		}
		$this->messages[] = "Success";
		return true;
	}


	/**
	 * Login into system from lockscreen
	 * @param  string  $pwd      
	 * @param  boolean $remember 
	 * @return boolean            
	 */
	public function lockscreen($pwd, $remember = false){
		session_name( $this->sessionName );
		session_start();

		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Get user's token
		if (!($token = $this->getToken())) {
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			return false;
		}

		// Check token's existance
		$tokenInfo = $this->__DB->getTokenInfo($token);
		if (!($tokenInfo && 
			count($tokenInfo) == 1)) {
			$this->destroyToken(null);
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			$this->error  = true; 
			$this->status = "Wrong token";
			return false;
		}

		// Check token's timeout
		if ($tokenInfo[0]['diff'] > $this->authTime) {
			$this->destroyToken( (int)$tokenInfo[0]['id'] );
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			$this->error  = true; 
			$this->status = "Token expired";
			return false;
		}

		// Verification of identity of user's ip and token's ip
		if ($tokenInfo[0][ $this->__DBconfig['fTokenIp'] ] !== $_SERVER['REMOTE_ADDR']) {
			if ($this->checkIP == 'strict') {
				$this->destroyToken( (int)$tokenInfo[0]['id'] );
				if ($this->reroute) {
					$this->reroute( $this->loginPageUrl);
				}
				$this->error  = true; 
				$this->status = "Different ip";
				return false;

			} else {
				$this->messages[] = "Different ip";
			}
		}

		if ($pwd == ''){
			$this->error  = true; 
			$this->status = "Empty fields";
			return false;
		}
		$pwd = md5(md5($pwd));

		$userInfo = $this->__DB->getUserByIdP( $tokenInfo[0][$this->__DBconfig['fIdUser']], $pwd );
		if ($userInfo &&
			count($userInfo) == 1) {
			
			$this->destroyToken( (int)$tokenInfo[0]['id'] );

			session_name( $this->sessionName );
			session_start();

			$this->newToken($userInfo[0]['id'], $remember);

			if ($this->reroute) {
				if ($this->lockRef &&
					isset($_COOKIE[ $this->lockRef_Name ])){

					$ref = $_COOKIE[ $this->lockRef_Name ];
					setcookie( $this->lockRef_Name, '', time() -3600 );
					$this->reroute( $_COOKIE[ $this->lockRef_Name ] );
				}

				$this->reroute( $this->successUrl );
			}

			$this->messages[] = "Success";
			return true;
		} else {
			$this->error  = true; 
			$this->status = "Wrong password";
			return false;
		}
	}


	/**
	 * Check current login status
	 * @return boolean 
	 */
	public function check(){
		session_name( $this->sessionName );
		session_start();

		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Get user's token
		if (!($token = $this->getToken())) {
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			return false;
		}

		// Check token's existance
		$tokenInfo = $this->__DB->getTokenInfo($token);
		if (!($tokenInfo && 
			count($tokenInfo) == 1)) {
			$this->destroyToken(null);
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			$this->error  = true; 
			$this->status = "Wrong token";
			return false;
		}
		
		// Check token's timeout
		if ((int)$tokenInfo[0]['diff'] > $this->authTime) {
			$this->destroyToken( (int)$tokenInfo[0]['id'] );
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			$this->error  = true; 
			$this->status = "Token expired";
			return false;
		}

		// Verification of identity of user's ip and token's ip
		if ($tokenInfo[0][ $this->__DBconfig['fTokenIp'] ] !== $_SERVER['REMOTE_ADDR']) {
			if ($this->checkIP == 'strict') {
				$this->destroyToken( (int)$tokenInfo[0]['id'] );
				if ($this->reroute) {
					$this->reroute( $this->loginPageUrl);
				}
				$this->error  = true; 
				$this->status = "Different ip";
				return false;

			} elseif ($this->checkIP == 'to_lockscreen' && 
					  $this->reroute) {
				$this->reroute( $this->lockscreenPageUrl );

			} else {
				$this->messages[] = "Different ip";
			}
		}

		// Check connected user
		$userInfo = $this->__DB->getUserInfoById($tokenInfo[0][ $this->__DBconfig['fIdUser'] ]);
		if ($userInfo &&
			count($userInfo) == 1) {
			
			if ($this->lockscreen &&
				$tokenInfo[0]['diff'] > $this->lockDelay) {
				if ($this->reroute) {
					$this->reroute( $this->lockscreenPageUrl);
				}
				$this->error  = true; 
				$this->status = "Lockscreen timeout";
				return false;
			} else {
				$this->messages[] = "Success";
				return true;
			}
		} else {
			$this->destroyToken( $tokenInfo[0]['id'] );
			if ($this->reroute) {
				$this->reroute( $this->loginPageUrl);
			}
			$this->error  = true; 
			$this->status = "Wrong token";
			return false;
		}
	}


	/**
	 * Create new token
	 * @param  string|int  $old      
	 * @param  int  	   $id_user  
	 * @param  boolean     $remember
	 */	
	private function newToken($id_user, $remember){
		$newToken = md5(md5( $this->generate(30) ));

		$this->__DB->insertToken($newToken, $id_user);

		if ($remember) {
			$cookie = setcookie($this->hashName, 
								$newToken, 
								time() + $this->authTime, 
								$this->cookiePath);
		} else {
			$_SESSION[ $this->hashName ] = $newToken;
		}
	}


	/**
	 * Get user's token
	 * @return string|boolean
	 */
	private function getToken(){
		if (session_status() === PHP_SESSION_ACTIVE &&
			isset($_SESSION[ $this->hashName ])){
			return $_SESSION[ $this->hashName ];

		} elseif (isset($_COOKIE[ $this->hashName ])) {
			return $_COOKIE[ $this->hashName ];

		} else {
			$this->error  = true; 
			$this->status = "Token not found";
			return false;
		}
	}


	/**
	 * Delete existing token from user's side
	 */
	private function destroyToken($id){
		session_unset();
		setcookie( $this->hashName, '', time() - 3600, $this->cookiePath );

		if (!is_null($id)) {
			$this->_db_deleteToken( (int)$id );
		}
	}


	/**
	 * Reroutes to the defined page
	 * @param  string $url
	 */
	public function reroute($url){
		if (stripos($url, substr($_SERVER['PHP_SELF'], 1) ) === false) {
			header("Location: " . $url); 
			die("Rerouted to " . $url);
		}
	}


	/**
	 * Generates random string
	 * @param  integer $length
	 * @return string
	 */
	private function generate($length){
		$arr = array('a','b','c','d','e','f',
				     'g','h','i','j','k','l',
				     'm','n','o','p','r','s',
				     't','u','v','x','y','z',
				     'A','B','C','D','E','F',
				     'G','H','I','J','K','L',
				     'M','N','O','P','R','S',
				     'T','U','V','X','Y','Z',
				     '1','2','3','4','5','6',
				     '7','8','9','0');
	    $str = "";
	    for($i = 0; $i < $length; $i++){
	        $index = rand(0, count($arr) - 1);
	        $str  .= $arr[$index];
	    }
	    return $str;
	}
}


?>