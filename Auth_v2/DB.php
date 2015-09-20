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

trait DB{

	private function _connect($host, $db_name, $login, $pwd){
		try {
			if (in_array("mysql", \PDO::getAvailableDrivers())) {
				$this->__DB = new \PDO("mysql:host=$host;dbname=$db_name", $login, $pwd);
				$this->__DB->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			} else {
				throw new Auth_exception('System does not support PDO MySQL');
			}
		} catch (\PDOException $e) {
			$this->error  = true; 
			$this->status = $e->getMessage();
		} catch (Auth_exception $e) {
			$this->error  = true;
			$this->status = $e->getMessage();
		}
	}

	private function _get($query, $params = null){}

	private function _set($query, $params = null){}

	private function _db_deleteToken($find){}

	private function _db_insertToken($token, $id_user){}

	private function _db_getToken($find){}

	private function _db_getUser($find, $data){}
	
}

?>