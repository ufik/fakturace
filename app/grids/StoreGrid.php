<?php

namespace Grid;

use \NiftyGrid\Grid;

class StoreGrid extends Grid{

    protected $store;

    public function __construct($store)
    {
        parent::__construct();
        $this->store = $store;
    }

    protected function configure($presenter)
    {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\NDataSource($this->store->select('id_store, name, date, id_user'));
        //Předáme zdroj
        $this->setDataSource($source);
        
        $this->setDefaultOrder("store.id_store DESC");
        
        $this->addColumn('name', 'Název dokladu');
        $this->addColumn('date', 'Datum vytvoření', '100px');
        $this->addColumn('id_user', 'Vlastník', '100px');
        
        $self = $presenter;
        
        $this->addButton("print", "Tisk PDF")
        ->setAjax(false)
        ->setClass("print new-page")
        ->setLink(function($row) use ($self){
        	return $self->link("printStore", $row['id_store']);
        });
        
        $this->addButton("edit", "Upravit")
        ->setClass("edit")
        ->setLink(function($row) use ($self){
        	return $self->link("addStore", $row['id_store']);
        });
        
        $this->addButton("delete", "Smazat")
        ->setClass("delete")
        ->setLink(function($row) use ($self){
        	return $self->link("delete!", $row['id_store']);
        })->setConfirmationDialog(function($row){
        	return "Určitě chcete odstranit doklad $row[name]?";
        });
    }
}