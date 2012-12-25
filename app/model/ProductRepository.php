<?php
namespace Todo;
use Nette;

/**
 * Tabulka product
 */
class ProductRepository extends Repository{
	
	public function createProduct($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteProduct($idProduct){
		return $this->getTable()->where('id_product = ?', $idProduct)->delete();
	}
	
	public function updateProduct($data){
		return $this->getTable()->where('id_product = ?', $data["id_product"])->update($data);
	}
	
	public function findProductsByName($term){
		return $this->getTable()->where("name LIKE ?", "%$term%");
	}
	
}