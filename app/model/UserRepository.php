<?php
namespace Todo;
use Nette;

/**
 * Tabulka serie
 */
class UsersRepository extends Repository{
	
	public function createUser($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteUser($idUser){
		return $this->getTable()->where('id = ?', $idUser)->delete();
	}
	
	public function updateUser($data){
		return $this->getTable()->where('id = ?', $data["id"])->update($data);
	}
	
	public function getUser($idUser){
		return $this->getTable()->find($idUser)->fetch();
	}
}