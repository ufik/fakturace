<?php

namespace App;

use Nette,
	Model, Grid\ProductGrid;


/**
 * Products presenter.
 */
class ProductsPresenter extends BasePresenter
{
	
	private $productRepository;
	
	protected function startup(){
		parent::startup();
		$this->productRepository = $this->context->productRepository;
	}
	
	public function renderDefault(){
		
	}
	
	public function renderAddProduct($idProduct){}
	
	protected function createComponentProductGrid(){
		return new ProductGrid($this->context->productRepository);
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentProductForm(){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
	
		$form->addText('name', 'Název:')
		->setRequired('Prosím zadejte název produktu.');
	
		$form->addText('price', 'Cena:');
		
		$form->addText('mj', 'Měrná jednotka:');
	
		$form->addText('catalogue_code', 'Katalogové číslo:');
	
		$form->addSubmit('send', 'Uložit produkt')->getControlPrototype()->class('btn btn-success');
		
		$form->setDefaults(array("mj" => "ks"));
		
		if(array_key_exists('idProduct', $_GET)){
			$product = $this->productRepository->findBy(array("id_product" => $_GET["idProduct"]))->fetch();
	
			$form->setDefaults($product->toArray());
			$form->addHidden('id_product', $product->id_product);
		}
	
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->productFormSucceeded;
		return $form;
	}
	
	public function productFormSucceeded($form){
		$values = $form->getValues();
	
		if(array_key_exists('idProduct', $_GET)){
	
			$update = $this->productRepository->updateProduct($values);
	
			if($update){
				$this->presenter->flashMessage('Produkt byl upraven.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit produkt.', 'error');
			}
		}else{
	
			if($this->productRepository->createProduct($values)){
	
				$this->presenter->flashMessage('Produkt byl uložen.', 'ok');
	
				if($this->isAjax()){
					$this->payload->clearForm = true;
				}else{
					$this->redirect('Products:');
				}
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit produkt.', 'error');
			}
	
		}
	}
	
	public function handleDelete($idProduct){
	
		if($this->productRepository->deleteProduct($idProduct)){
			$this->presenter->flashMessage("Produkt byl smazán.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat produkt.", "error");
		}
	
	
	}
}
