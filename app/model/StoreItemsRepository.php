<?php
namespace Todo;
use Nette;

/**
 * Tabulka polozek dokladu
 */
class StoreItemsRepository extends Repository{
	
	
	public function insert($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteItems($idStore){
		return $this->getTable()->where('id_store = ?', $idStore)->delete();
	}
	
	public function getItems($idStore){
		return $this->getTable()->where('id_Store = ?', $idStore);
	}
	
}