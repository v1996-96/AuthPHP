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


require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Functions.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'DB.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Auth.php';


abstract class Base
{
	// An instance of a plugin
	private static $__instance = null;

	// Plugin main variables
	protected $error  = false,
			  $status = '',
			  $allowedRoles = array(),
			  $__DB   = null;


	/**
	 * Makes an instance of a plugin
	 * @return object
	 */
	public static function instance(){
		if(is_null(self::$__instance))
			self::$__instance = new Auth();
		return self::$__instance;
	}


	/**
	 * Makes new instance of a plugin
	 * @return object
	 */
	public static function newInstance(){
		return new Auth();
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
	 * Get configuration from ini file
	 * @param  string $path Path to ini file
	 * @return bool         parsing status  
	 */
	public function iniConfig($path){
		if (is_file($path)) {
			$config = @parse_ini_file($path, true);
			if (!$config) return false;

			if (isset($config["config"]))
				$this->config( $config["config"] );

			if (isset($config["DBconfig"]))
				$this->DBconfig( $config["DBconfig"] );

			return true;
		} else
			return false;
	}


	/**
	 * Changes the plugin configuration
	 * @param  array  $new New fields
	 */
	public function config($new = array()){
		foreach ($new as $key => $value) {
			if (array_key_exists($key, $this->__config)) {
				$this->__config = array_replace($this->__config, $new);
			}
		}
	}


	/**
	 * Changes the db configuration
	 * @param array $new New fields
	 */
	public function DBconfig($new = array()){
		foreach ($new as $key => $value) {
			if (array_key_exists($key, $this->__DBconfig)) {
				$this->__DBconfig = array_replace($this->__DBconfig, $new);
			}
		}
	}


	/**
	 * Plugin default configuration
	 * @var array
	 */
	protected $__config = array(
		'makeLog'			 => true,	  # logfile name: log.txt. Access should be prohibited in .htaccess
		'hashName'           => 'token',
		'cookiePath'         => '/',
		'authTime'           => 10800,
		'lockDelay'          => 1200,
		'checkIPToken'       => 'strict', # strict | acceptable | to_lockscreen
		'multiple'           => true, 	  # allow multiple connections
		'onMultiple'         => 'allow',  # allow (just rewrite token) | discard (@user is already loged in) :: only if multiple == false
		'reroute'            => false,
		'useLockscreen'      => true,
		'loginPageUrl'       => '/',
		'lockscreenPageUrl'  => '/lockscreen',
		'successUrl'         => '/dashboard',
		'lockscreenRef'      => false,	  # false | cookie name that contains referer url
		'IPList'			 => 'black',  # use white list or black
		'IPWhiteList'		 => array(),
		'IPBlackList'		 => array(),
		'onRoleMismatch'	 => false
		);


	/**
	 * Database default configuration
	 * @var array
	 */
	protected $__DBconfig = array(
		'tUserInfo'  => 'user',		# users table name
		'tUserToken' => 'token',	# token table name
		'fLogin'     => 'login',	# login field name in users table 
		'hashLogin'  => true,		# hash login or not
		'fPassword'  => 'pwd',		# password filed name in users table
		'fRole'		 => 'role',		# role filed name in users table
		'fIdUser'    => 'id_user',  # user's id field name in token table
		'fToken'     => 'token',	# token field name in token table
		'fTokenIp'   => 'user_ip',	# user's ip field name in token table
		'fTokenAdd'  => 'time_add'	# token's time add field name in token table
		);
}

return Base::instance();


?>