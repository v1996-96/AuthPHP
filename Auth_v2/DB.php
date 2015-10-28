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

	/**
	 * Connect plugin to database
	 * @param  string $host    
	 * @param  string $db_name 
	 * @param  string $login   
	 * @param  string $pwd
	 */
	public function connect($host, $db_name, $login, $pwd){
		try {
			if (in_array("mysql", \PDO::getAvailableDrivers())) {
				$this->__DB = new \PDO("mysql:host=$host;dbname=$db_name", $login, $pwd);
				$this->__DB->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			} else {
				$this->_setError('System does not support PDO MySQL');
			}
		} 
		catch (\PDOException $e) {
			$this->_setError( $e->getMessage() );
		}
	}


	/**
	 * Get the information from DB in form of array
	 * @param  string $query  SQL query
	 * @param  array  $params Array of binding parameters
	 * @return array|bool     Server response
	 */
	private function _db_get($query, $params = null){
		if ($this->error) return null;
		
		try {
			$sth = $this->__DB->prepare($query);
			$sth->setFetchMode(\PDO::FETCH_ASSOC);
			$sth->execute($params);
			return $sth->fetchAll();

		} catch (PDOException $e) {
			$this->_setError( $e->getMessage() );
			return null;
		}
	}


	/**
	 * Insert data into DB or manipulate with data
	 * @param  string $query  SQL query
	 * @param  array  $params Array of binding parameters
	 * @return array|bool     Server response
	 */
	private function _db_set($query, $params = null){
		if ($this->error) return null;

		try {
			$sth = $this->__DB->prepare($query);
			return $sth->execute($params);

		} catch (PDOException $e) {
			$this->_setError( $e->getMessage() );
			return null;
		}
	}


	/**
	 * Delete token from DB with different search parameters
	 * @param  string|int|array $find Token|Token_ID|UserInfo
	 * @return bool       
	 */
	private function _db_deleteToken($find){
		if ($this->error) return null;

		// Searching by token
		if (is_string($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fToken.' = ?',
							  	  array( $find ));

		// Searching by token id
		} elseif (is_int($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE id = ?',
							  	  array( $find ));

		// Searching by user id
		} elseif (is_array($find)) {
			return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fIdUser.' = ?',
							  	  array( $find['id'] ));
		} else
			return null;
	}


	/**
	 * Deleting expired tokens
	 * @param  integer $authTime Token expiration delay
	 * @return bool
	 */
	private function _db_deleteOldToken($authTime){
		if ($this->error) return null;

		return $this->_db_set('DELETE FROM '.$this->tUserToken.
							  ' WHERE TIMESTAMPDIFF(SECOND, 
							  '.$this->tUserToken.'.'.$this->fTokenAdd.', 
							  NOW()) > '.$authTime);
	}


	/**
	 * Insert new token into DB
	 * @param  string $token   Token itself
	 * @param  int    $id_user User id
	 * @return bool
	 */
	private function _db_insertToken($token, $id_user){
		if ($this->error) return null;

		return $this->_db_set('INSERT INTO '.$this->tUserToken.
							  ' ('.$this->fToken.', '.$this->fIdUser.', '.$this->fTokenIp.') 
							  VALUES (?, ?, ?)',
							  array( $token, (int)$id_user, $_SERVER['REMOTE_ADDR'] ));
	}


	/**
	 * Get token by search parameter
	 * @param  string|int $find Search parameter
	 * @return array|bool|null  Token data
	 */
	private function _db_getToken($find){
		if ($this->error) return null;

		$this->_db_deleteOldToken( $this->authTime );

		// Searching by token
		if (is_string($find)) {
			return $this->_db_get('SELECT *, 
							  	   TIMESTAMPDIFF(SECOND, 
									    '.$this->tUserToken.'.'.$this->fTokenAdd.', 
									    NOW()) as diff
							  	   FROM '.$this->tUserToken.
							 	   ' WHERE '.$this->fToken.' = ?',
							 	   array($find));

		// Searching by user id
		} elseif (is_int($find)) {
			return $this->_db_get('SELECT * FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fIdUser.' = ?',
							  	  array($find));
		} else
			return null;
	}


	/**
	 * Get user info by search parameter
	 * @param  string $find Type of reqquest
	 * @param  array  $data Search parameters
	 * @return array|bool|null  User info
	 */
	private function _db_getUser($find, $data){
		if ($this->error) return null;

		// Searching by id
		if ($find == "id") {
			return $this->_db_get('SELECT id, '.$this->fRole.' 
							  	  FROM '.$this->tUserInfo.
							  	  ' WHERE id = ?',
							  	  array( (int)$data["id"] ));

		// Searching by login and password
		} elseif ($find == "LoginPassword") {
			return $this->_db_get('SELECT id, '.$this->fRole.'
							  	  FROM '.$this->tUserInfo.
							  	  ' WHERE '.$this->fLogin.' = :login'.
							  	  ' AND '.$this->fPassword.' = :pwd',
							 	   array( 'login' => $data['login'], 'pwd' => $data['pwd'] ));

		// Searching by id and password
		} elseif ($find == "IdPassword") {
			return $this->_db_get('SELECT id, '.$this->fRole.' 
						  		  FROM '.$this->tUserInfo.
						  		  ' WHERE id = ?'.
						  		  ' AND '.$this->fPassword.' = ?',
						  		  array( (int)$data["id"], $data['pwd'] ));
		} else
			return null;
	}
	
}

?>