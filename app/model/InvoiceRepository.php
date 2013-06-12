<?php
namespace Todo;
use Nette;

/**
 * Tabulka faktur
 */
class InvoiceRepository extends Repository{
	
	public function createInvoice($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteInvoice($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice)->delete();
	}
	
	public function updateInvoice($data){
		return $this->getTable()->where('id_invoice = ?', $data["id_invoice"])->update($data);
	}
	
	public function getInvoice($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice)->fetch();
	}
	
	public function getInvoicesByCurrency($curr = "CZK", $from = null, $to = null){
	    $query = $this->getTable()->where("mena", $curr);
	    
	    if($from != null)
		$query->where("datum_vystaveni >= ?", $from);
	    
	    if($to != null)
		$query->where("datum_vystaveni < ?", $to);
	    
	    $query->order("datum_vystaveni DESC");
	    
	    return $query;
	}
	
	
}