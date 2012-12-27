<?php

namespace Grid;

use \NiftyGrid\Grid;

class ContactGrid extends Grid{

    protected $contact;

    public function __construct($contact)
    {
        parent::__construct();
        $this->contact = $contact;
    }

    protected function configure($presenter)
    {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\NDataSource($this->contact->select('id_contact, name, ico, dic, ulice, mesto'));
        //Předáme zdroj
        $this->setDataSource($source);
        
        $this->setDefaultOrder("contact.id_contact DESC");
        
        $this->setPerPageValues(array(10, 20, 50, 100));
      
        $this->addColumn('name', 'Jméno společnosti')->setTextFilter();
        $this->addColumn('ico', 'IČO', '100px');
        $this->addColumn('dic', 'DIČ', '100px');
        $this->addColumn('ulice', 'Ulice', '100px')->setTextFilter();
        $this->addColumn('mesto', 'Město', '100px')->setTextFilter();
        
        $self = $presenter;
        
        $this->addButton("edit", "Upravit")
        ->setClass("edit")
        ->setLink(function($row) use ($self){
        	return $self->link("addContact", $row['id_contact']);
        });
        
        $this->addButton("delete", "Smazat")
        ->setClass("delete")
        ->setLink(function($row) use ($self){
        	return $self->link("delete!", $row['id_contact']);
        })->setConfirmationDialog(function($row){
        	return "Určitě chcete odstranit kontakt $row[name]?";
        });
    }
}