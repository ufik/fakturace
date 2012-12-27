<?php

namespace Grid;

use \NiftyGrid\Grid;

class ProductGrid extends Grid{

    protected $product;

    public function __construct($product)
    {
        parent::__construct();
        $this->product = $product;
    }

    protected function configure($presenter)
    {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\NDataSource($this->product->select('id_product, name, catalogue_code, price'));
        //Předáme zdroj
        $this->setDataSource($source);
        
        $this->setDefaultOrder("product.id_product DESC");
        
        $this->setPerPageValues(array(10, 20, 50, 100));
        
        $this->addColumn('name', 'Název produktu')->setTextFilter();
        $this->addColumn('catalogue_code', 'Katalogové číslo', '100px')->setTextFilter();
        $this->addColumn('price', 'Cena', '100px')->setNumericFilter();
        
        $self = $presenter;
        
        $this->addButton("edit", "Upravit")
        ->setClass("edit")
        ->setLink(function($row) use ($self){
        	return $self->link("addProduct", $row['id_product']);
        });
        
        $this->addButton("delete", "Smazat")
        ->setClass("delete")
        ->setLink(function($row) use ($self){
        	return $self->link("delete!", $row['id_product']);
        })->setConfirmationDialog(function($row){
        	return "Určitě chcete odstranit produkt $row[name]?";
        });
    }
}