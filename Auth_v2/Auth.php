<?php

/**
 * PHP Authorization Plugin
 *
 * This plugin implements a lot of necessary functionality
 * without using any framework.
 *
 * @author  Trushin Victor <v1996-96@mail.ru>
 * @version 2.0 latest
 * @copyright (c) 2015 Trudhin Victor. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl GNU GENERAL PUBLIC LICENSE v3
 */

namespace Auth_v2;

require_once '../Auth_v2/Captcha.php';
require_once '../Auth_v2/Functions.php';
require_once '../Auth_v2/Messages.php';
require_once '../Auth_v2/Session.php';
require_once '../Auth_v2/DB.php';

class Auth extends Base
{
	// Connecting traits
	use Functions, Captcha, Messages, Session, DB;


	function __construct(){
		// there we should parse config.ini and start session
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


	public function login($login, $pwd, $remember = false){}
	
	public function lockscreen($pwd, $remember = false){}

	public function check(){}

	public function checkRole($role){}

	public function register($login, $password, $email = "", $role = false, $fields = array()){}

	public function restore($email){}

	public function confirmReg($code){}
}

?>