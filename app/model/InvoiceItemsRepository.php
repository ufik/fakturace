<?php
namespace Todo;
use Nette;

/**
 * Tabulka faktur
 */
class InvoiceItemsRepository extends Repository{
	
	// @fixme presunout na obecnejsi misto
	private static $CZK = "CZK";
	
	private static $EUR = "EUR";
	
	public function insert($data){
		return $this->getTable()->insert($data);
	}
	
	public function deleteItems($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice)->delete();
	}
	
	public function getInvoiceItems($idInvoice){
		return $this->getTable()->where('id_invoice = ?', $idInvoice);
	}
	
	public function getPriceSum(){
		$sum = 0;
		$items = $this->getTable();
		
		foreach ($items as $item){
			$sum += $item->cena * $item->pocet;
		}
		
		
		return $sum;
	}
}