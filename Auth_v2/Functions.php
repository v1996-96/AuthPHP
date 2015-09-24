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
	

	private function _getUserRole($id_user){}


	private function _newToken($id_user, $remember){}


	private function _getToken(){}


	private function _destroyToken($id){}


	private function _hash($str){
		return md5(md5($str));
	}


	private function _generate($length){
		$chars = 'abdefhknrstyz23456789';
		$code = '';
		for ($i=0; $i < $length; $i++)
			$code .= substr($chars, rand(1, strlen($chars))-1, 1);

		return $code;
	}


	private function _setError($message, $reroutePage = ""){
		if ($this->reroute && $reroutePage !== "") 
			$this->reroute( $reroutePage );

		$this->error  = true; 
		$this->status = $message;
		$this->_log($message, "Error");
		return false;
	}


	public function _log($message, $messageType = "Message"){
		if (!$this->makeLog) return;

		// we should check the file's weight
		// if it is too big we need to make new file

		$filename = __REFERANCE__."log.txt";
		$message = "Time:" . date("Y-m-d H:i:s") . " | Type:" . $messageType . " | Message:" . $message . "\r\n";
		$f = fopen($filename, "a+");
		if ($f && is_writable($filename)) {
			fwrite($f, $message);
			fclose($f);
		}
	}


	public function reroute($url){
		if (stripos($url, substr($_SERVER['PHP_SELF'], 1) ) === false) {
			header("Location: " . $url); 
			die("Rerouted to " . $url);
		}
	}
	
}

?>