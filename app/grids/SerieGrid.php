<?php

namespace Grid;

use \NiftyGrid\Grid;

class SerieGrid extends Grid{

    protected $serie;

    public function __construct($serie)
    {
        parent::__construct();
        $this->serie = $serie;
    }

    protected function configure($presenter)
    {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\NDataSource($this->serie->select('id_serie, name, serie, pattern'));
        //Předáme zdroj
        $this->setDataSource($source);
        
        $this->setDefaultOrder("serie.id_serie DESC");
        
        $this->addColumn('name', 'Název řady');
        $this->addColumn('serie', 'Aktuální hodnota řady', '100px');
        $this->addColumn('pattern', 'Vzor', '100px');
        
        $self = $presenter;
        
        $this->addButton("edit", "Upravit")
        ->setClass("edit")
        ->setLink(function($row) use ($self){
        	return $self->link("addSerie", $row['id_serie']);
        });
        
        $this->addButton("delete", "Smazat")
        ->setClass("delete")
        ->setLink(function($row) use ($self){
        	return $self->link("delete!", $row['id_serie']);
        })->setConfirmationDialog(function($row){
        	return "Určitě chcete odstranit řadu $row[name]?";
        });
    }
}