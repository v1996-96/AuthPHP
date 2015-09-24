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

trait DB{

	private function _connect($host, $db_name, $login, $pwd){
		try {
			if (in_array("mysql", \PDO::getAvailableDrivers())) {
				$this->__DB = new \PDO("mysql:host=$host;dbname=$db_name", $login, $pwd);
				$this->__DB->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			} else {
				throw new Auth_exception('System does not support PDO MySQL');
			}
		} 
		catch (\PDOException $e) {
			$this->_setError( $e->getMessage() );
		} 
		catch (Auth_exception $e) {
			$this->_setError( $e->getMessage() );
		}
	}


	private function _db_get($query, $params = null){
		if ($this->error) return null;

		try {
			$sth = $this->__db->prepare($query);
			$sth->setFetchMode(\PDO::FETCH_ASSOC);
			$sth->execute($params);
			return $sth->fetchAll();

		} catch (PDOException $e) {
			$this->_setError( $e->getMessage() );
			return null;
		}
	}


	private function _db_set($query, $params = null){
		if ($this->error) return null;

		try {
			$sth = $this->__db->prepare($query);
			return $sth->execute($params);

		} catch (PDOException $e) {
			$this->_setError( $e->getMessage() );
			return null;
		}
	}


	private function _db_deleteToken($find){
		if ($this->error) return null;

		if (is_string($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fToken.' = ?',
							  	  array( $find ));

		} elseif (is_int($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE id = ?',
							  	  array( $find ));

		} elseif (is_array($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fIdUser.' = ?',
							  	  array( $find['id'] ));
		} else
			return null;
	}


	private function _db_insertToken($token, $id_user){
		if ($this->error) return null;

		return $this->_db_set('INSERT INTO '.$this->tUserToken.
							  ' ('.$this->fToken.', '.$this->fIdUser.', '.$this->fTokenIp.') 
							  VALUES (?, ?, ?)',
							  array( $token, $id_user, $_SERVER['REMOTE_ADDR'] ));
	}


	private function _db_getToken($find){
		if ($this->error) return null;

		if (is_string($find)) {
			return $this->_db_get('SELECT *, 
							  	   TIMESTAMPDIFF(SECOND, 
									    '.$this->tUserToken.'.'.$this->fTokenAdd.', 
									    NOW()) as diff
							  	   FROM '.$this->tUserToken.
							 	   ' WHERE '.$this->fToken.' = ?',
							 	   array($find));

		} elseif (is_int($find)) {
			return $this->_db_get('SELECT * FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fIdUser.' = ?',
							  	  array($find));
		} else
			return null;
	}


	private function _db_getUser($find, $data){
		if ($this->error) return null;

		if ($find == "id") {
			return $this->_db_get('SELECT id 
							  	  FROM '.$this->tUserInfo.
							  	  ' WHERE id = ?',
							  	  array( (int)$data["id"] ));

		} elseif ($find == "LoginPassword") {
			return $this->_db_get('SELECT id
							  	  FROM '.$this->tUserInfo.
							  	  ' WHERE '.$this->fLogin.' = :login'.
							  	  ' AND '.$this->fPassword.' = :pwd',
							 	   array( 'login' => $data['login'], 'pwd' => $data['pwd'] ));

		} elseif ($find == "IdPassword") {
			return $this->_db_get('SELECT id 
						  		  FROM '.$this->tUserInfo.
						  		  ' WHERE id = ?'.
						  		  ' AND '.$this->fPassword.' = ?',
						  		  array( (int)$data["id"], $data['pwd'] ));
		} else
			return null;
	}
	
}

?>