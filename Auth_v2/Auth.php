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

require_once __REFERANCE__.'Captcha.php';
require_once __REFERANCE__.'Functions.php';
require_once __REFERANCE__.'Messages.php';
require_once __REFERANCE__.'Session.php';
require_once __REFERANCE__.'DB.php';

class Auth extends Base
{
	// Connecting traits
	use Functions, Captcha, Messages, Session, DB;


	function __construct(){
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

		// Check fields
		if ($login == '' || $pwd == '')
			return $this->_setError("Empty fields");
		
		// Hashing fields
		if ($this->hashLogin) $login = $this->_hash($login);
		$pwd = $this->_hash($pwd);

		// Check incoming data
		$userInfo = $this->_db_getUser("LoginPassword", array( 'login' => $login, 'pwd'   => $pwd ));
		if (!($userInfo &&
			count($userInfo) == 1)) {
			return $this->_setError("Wrong login or password");
		}

		// If multiple connections are prohibited
		$tokenInfo = $this->_db_getToken( (int)$userInfo[0]['id'] );
		if (!$this->multiple) {
			if ($this->onMultiple == 'allow') {
				$this->_db_deleteToken( $userInfo[0] );

			} elseif ($tokenInfo && count($tokenInfo) >= 1) {
				return $this->_setError("User is already logged in");
			}
		}

		// Check user's role
		// ********************************************

		// Create new token
		$this->newToken( $userInfo[0]['id'], $remember );

		if ($this->reroute)
			$this->reroute( $this->successUrl );

		$this->_log("Successful login by user #".$userInfo[0]['id'], "Success");
		return true;
	}
	


	public function lockscreen($pwd, $remember = false){}



	public function check(){}



	public function setAllowedRoles($roles){}
}

?>