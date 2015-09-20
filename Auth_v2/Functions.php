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

trait Functions{
	
	private function _getUserRole(){}

	private function _newToken($id_user, $remember){}

	private function _getToken(){}

	private function _destroyToken($id){}

	private function _hash($str){}

	private function _generate($length){}

	private function _error($reroutePage, $status){}

	private function _sendEmail(){}

	private function _log($message){}

	public function reroute($url){}
	
}

?>