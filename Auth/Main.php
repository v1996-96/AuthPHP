<?php

namespace Auth;



/**
 * Main plugin class
 */
abstract class Main
{

	public static function instance(){
		require_once 'Auth/Auth.php';
		require_once 'Auth/DB.php';

		return new Auth();
	}

	public function config($new = array()){
		foreach ($new as $key => $value) {
			if (array_key_exists($key, $this->__config)) {
				$this->__config = array_replace($this->__config, $new);
			}
		}
	}

	public function DBconfig($new = array()){
		foreach ($new as $key => $value) {
			if (array_key_exists($key, $this->__DBconfig)) {
				$this->__DBconfig = array_replace($this->__DBconfig, $new);
			}
		}
	}

	protected $__config = array(
		'sessionName'       => 'SESSION',
		'hashName'          => 'token',
		'cookiePath'        => '/',
		'authTime'          => 10800,
		'lockDelay'         => 1200,
		'checkIP'			=> 'strict', # strict | acceptable | to_lockscreen
		'multiple'			=> true, 	 # allow multiple connections
		'onMultiple'		=> 'allow',  # allow | discard  only if multiple == false
		'reroute'           => false,
		'lockscreen'        => true,
		'loginPageUrl'      => '/',
		'lockscreenPageUrl' => '/lockscreen',
		'successUrl'        => '/dashboard',
		'lockRef'			=> true,
		'lockRef_Name'		=> 'referer'

		// need for ip white list
		);

	protected $__DBconfig = array(
		'tUserInfo'  => 'user',
		'tUserToken' => 'token',
		'fLogin'     => 'login',
		'hashLogin'  => true,
		'fPassword'  => 'pwd',
		'fIdUser'    => 'id_user',
		'fToken'     => 'token',
		'fTokenIp'   => 'user_ip',
		'fTokenAdd'  => 'time_add'
		);
}



/**
 * Plugin exception
 */
class Auth_exception extends \Exception 
{
	public function __construct($message = null, $code = 0){
		if (!$message) {
            throw new $this('Unknown exception');
        }
        parent::__construct($message, $code);

        // log
	}
}

return Main::instance();

?>