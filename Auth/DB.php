<?php

namespace Auth;



/**
 * Database system
 */
class DB extends Main
{
	private $error  = false,
			$status = '',
			$db     = null;

	function __get($attr){
		if (array_key_exists($attr, $this->__DBconfig)) {
			return $this->__DBconfig[ $attr ];
		}
	}

	function __construct($host, $db_name, $login, $pwd){
		try {
			if (in_array("mysql", \PDO::getAvailableDrivers())) {
				$this->db = new \PDO("mysql:host=$host;dbname=$db_name", $login, $pwd);
				$this->db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
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

	public function hasError(){
		return $this->error;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getData($query, $params = null){
		if ($this->error) return null;

		try {
			$sth = $this->db->prepare($query);
			$sth->setFetchMode(\PDO::FETCH_ASSOC);
			$sth->execute($params);
			return $sth->fetchAll();

		} catch (PDOException $e) {
			$this->error  = true; 
			$this->status = $e->getMessage();
			return null;
		}
	}

	public function setData($query, $params = null){
		if ($this->error) return null;

		try {
			$sth = $this->db->prepare($query);
			return $sth->execute($params);

		} catch (PDOException $e) {
			$this->error  = true; 
			$this->status = $e->getMessage();
			return null;
		}
	}

	public function deleteToken($find){
		if ($this->error) return null;

		if (is_string($find)) {
			return $this->setData('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fToken.' = ?',
							  	  array( $find ));
		} elseif (is_int($find)) {
			return $this->setData('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE id = ?',
							  	  array( $find ));
		} elseif (is_array($find)) {
			return $this->setData('DELETE FROM '.$this->tUserToken.
							  	  ' WHERE '.$this->fIdUser.' = ?',
							  	  array( $find['id'] ));
		} else
			return null;
	}

	public function insertToken($token, $id_user){
		if ($this->error) return null;

		return $this->setData('INSERT INTO '.$this->tUserToken.
							  ' ('.$this->fToken.', '.$this->fIdUser.', '.$this->fTokenIp.') 
							  VALUES (?, ?, ?)',
							  array( $token, $id_user, $_SERVER['REMOTE_ADDR'] ));
	}

	public function getTokenInfo($token){
		if ($this->error) return null;

		return $this->getData('SELECT *, 
							  TIMESTAMPDIFF(SECOND, 
								 			'.$this->tUserToken.'.'.$this->fTokenAdd.', 
								 			NOW()) as diff
							  FROM '.$this->tUserToken.
							  ' WHERE '.$this->fToken.' = ?',
							  array($token));
	}

	public function getTokenInfoUser($id_user){
		if ($this->error) return null;

		return $this->getData('SELECT * FROM '.$this->tUserToken.
							  ' WHERE '.$this->fIdUser.' = ?',
							  array((int)$id_user));
	}

	public function getUserInfoById($id){
		if ($this->error) return null;

		return $this->getData('SELECT id 
							  FROM '.$this->tUserInfo.
							  ' WHERE id = ?',
							  array( (int)$id ));
	}

	public function getUserInfoByLP($login, $pwd){
		if ($this->error) return null;

		return $this->getData('SELECT id
							  FROM '.$this->tUserInfo.
							  ' WHERE '.$this->fLogin.' = :login'.
							  ' AND '.$this->fPassword.' = :pwd',
							  array( 'login' => $login, 'pwd' => $pwd ));
	}

	public function getUserByIdP($id, $pwd){
		if ($this->error) return null;

		return $this->getData('SELECT id 
							  FROM '.$this->tUserInfo.
							  ' WHERE id = ?'.
							  ' AND '.$this->fPassword.' = ?',
							  array( (int)$id, $pwd ));
	}
}


?>