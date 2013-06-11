<?php
namespace Todo;
use Nette;

/**
 * Tabulka skladu
 */
class StoreRepository extends Repository{
	
	public function createStore($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteStore($idStore){
		return $this->getTable()->where('id_store = ?', $idStore)->delete();
	}
	
	public function updateStore($data){
		return $this->getTable()->where('id_store = ?', $data["id_store"])->update($data);
	}
	
	public function getStore($idStore){
		return $this->getTable()->where('id_store = ?', $idStore)->fetch();
	}
}