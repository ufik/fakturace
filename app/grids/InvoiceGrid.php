<?php

namespace Grid;

use \NiftyGrid\Grid;

class InvoiceGrid extends Grid{

    protected $invoice;

    public function __construct($invoice)
    {
        parent::__construct();
        $this->invoice = $invoice;
    }

    protected function configure($presenter)
    {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\NDataSource($this->invoice->select('id_invoice, name, number, datum_vystaveni'));
        //Předáme zdroj
        $this->setDataSource($source);
        
        $this->setDefaultOrder("invoice.id_invoice DESC");
        
        $this->addColumn('name', 'Název faktury')
        	->setTextFilter();
	
	$this->addColumn('datum_vystaveni', 'Datum vystavení')
        	->setDateFilter();
        
        $this->addColumn('number', 'Variabilní číslo', '100px')
        	->setNumericFilter();
        
        $self = $presenter;
        
        $this->addButton("email", "Odeslat email s PDF fakturou")
        ->setClass("email")
        ->setLink(function($row) use ($self){
        	return $self->link("emailInvoice", $row['id_invoice']);
        });
        
        $this->addButton("print", "Tisk PDF")
        ->setAjax(false)
        ->setClass("print new-page")
        ->setLink(function($row) use ($self){
        	return $self->link("printInvoice", $row['id_invoice']);
        });
        
        $this->addButton("edit", "Upravit")
        ->setClass("edit")
        ->setLink(function($row) use ($self){
        	return $self->link("addInvoice", $row['id_invoice']);
        });
        
        $this->addButton("delete", "Smazat")
        ->setClass("delete")
        ->setLink(function($row) use ($self){
        	return $self->link("delete!", $row['id_invoice']);
        })->setConfirmationDialog(function($row){
        	return "Určitě chcete odstranit fakturu $row[name]?";
        });
    }
}