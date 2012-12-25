<?php
namespace Todo;
use Nette;

/**
 * Tabulka faktur
 */
class InvoiceItemsRepository extends Repository{
	
	public function insert($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteItems($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice)->delete();
	}
	
	public function getInvoiceItems($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice);
	}
	
}