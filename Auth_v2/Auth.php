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

require_once __REFERANCE__.'Functions.php';
require_once __REFERANCE__.'Messages.php';
require_once __REFERANCE__.'DB.php';

class Auth extends Base
{
	// Connecting traits
	use Functions, Messages, DB;


	function __construct(){
		if(!session_id()) 
			session_start();
	}


    //! Quick access to settings
	function __get($attr){
		if (array_key_exists($attr, $this->__config)) {
			return $this->__config[ $attr ];
		} elseif (array_key_exists($attr, $this->__DBconfig)) {
			return $this->__DBconfig[ $attr ];
		} else
			return null;
	}



	public function login($login, $pwd, $remember = false){
		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Check user's ip
		if (!$this->_checkIPList())
			return false;

		// Check fields
		if ($login == '' || $pwd == '')
			return $this->_setError("Empty fields");
		
		// Hashing fields
		if ($this->hashLogin) $login = $this->_hash($login);
		$pwd = $this->_hash($pwd);

		// Check incoming data
		$userInfo = $this->_db_getUser("LoginPassword", array( 'login' => $login, 'pwd' => $pwd ));
		if (!($userInfo &&
			count($userInfo) == 1)) {
			return $this->_setError("Wrong login or password");

		} elseif ($this->allowedRoles && !in_array((int)$userInfo[0][ $this->fRole ], $this->allowedRoles)) {
			// Check if user has the right to visit page
			return $this->_setError("You have no right to visit this page");
		}

		// Check for multiple connections
		$tokenInfo = $this->_db_getToken( (int)$userInfo[0]['id'] );
		if (!$this->multiple) {
			if ($this->onMultiple == 'allow') {
				$this->_db_deleteToken( $userInfo[0] );

			} elseif ($tokenInfo && count($tokenInfo) >= 1) {
				return $this->_setError("User is already logged in");
			}
		}

		// Create new token
		$this->_newToken( $userInfo[0]['id'], $remember );

		// Make log
		$this->_log("Successful login by user #".$userInfo[0]['id'], "Success");

		if ($this->reroute)
			$this->reroute( $this->successUrl );

		return true;
	}
	


	public function lockscreen($pwd, $remember = false){
		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Check user's ip
		if (!$this->_checkIPList())
			return false;

		// Check current token
		if (!($tokenInfo = $this->_checkCurrentToken(true)))
			return false;

		// Check fields
		if ($pwd == '')
			return $this->_setError("Empty fields");

		// Get password hash
		$pwd = $this->_hash($pwd);

		// Check link with user
		$userInfo = $this->_db_getUser('IdPassword', 
				array( 'id' => $tokenInfo[0][ $this->fIdUser ], 'pwd' => $pwd ));
		if (!($userInfo && count($userInfo) == 1))
			return $this->_setError("Wrong password");

		// Delete old and create new token
		$this->_destroyToken( (int)$tokenInfo[0]['id'] );
		$this->_newToken( $userInfo[0]['id'], $remember );

		// Make log
		$this->_log("Successful login from lockscreen by user #".$userInfo[0]['id'], "Success");

		// Reroute to referer or success page
		if ($this->reroute){
			if ($this->lockscreenRef && isset($_COOKIE[ $this->lockscreenRef ])){
				$ref = $_COOKIE[ $this->lockscreenRef ];
				setcookie( $this->lockscreenRef, '', time() -3600 );
				$this->reroute( $_COOKIE[ $this->lockscreenRef ] );
			}
			$this->reroute( $this->successUrl );
		}

		return true;
	}



	public function check(){
		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Check user's ip
		if (!$this->_checkIPList())
			return false;

		// Check current token
		if (!($tokenInfo = $this->_checkCurrentToken()))
			return false;

		// Check link with user
		$userInfo = $this->_db_getUser('id', 
				array( 'id' => $tokenInfo[0][ $this->fIdUser ]));
		if (!($userInfo && count($userInfo) == 1)){
			$this->_destroyToken( (int)$tokenInfo[0]['id'] );
			return $this->_setError("Wrong link between user and token", $this->loginPageUrl);
		}

		return true;
	}



	public function logOut(){
		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		// Get current token
		$currentToken = $this->_getToken();
		if ($currentToken && $this->makeLog)
			$tokenInfo = $this->_db_getToken( $currentToken );
			if ($tokenInfo && count($tokenInfo) == 1) 
				$userInfo = $this->_db_getUser('id', array('id' => $tokenInfo[0][ $this->fIdUser ]) );

		// Destroy current token
		$this->_db_deleteToken( $currentToken );
		$this->_destroyToken(null);

		// Make log
		if ($this->makeLog && isset($userInfo) && $userInfo) {
			$userId = $userInfo[0]['id'];
		} else {
			$userId = "undefined";
		}
		$this->_log("User #" . $userId . " logged out", "Success");

		// Reroute to login page
		if ($this->reroute)
			$this->reroute( $this->loginPageUrl );

		return true;
	}



	public function setAllowedRoles($roles){
		// Check for existing errors
		if ($this->error || is_null($this->__DB))
			return false;

		if (is_int($roles)) {
			$this->allowedRoles = array( $roles );
			return true;

		} elseif (is_array($roles)) {
			$this->allowedRoles = $roles;
			return true;

		} else
			return false;
	}
}

?>