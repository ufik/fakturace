<?php
namespace Todo;
use Nette;

/**
 * Tabulka contact
 */
class ContactRepository extends Repository{
	
	public function createContact($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteContact($idContact){
		return $this->getTable()->where('id_contact = ?', $idContact)->delete();
	}
	
	public function updateContact($data){
		return $this->getTable()->where('id_contact = ?', $data["id_contact"])->update($data);
	}
	
}