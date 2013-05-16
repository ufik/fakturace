<?php

namespace App;

use Nette\DateTime;

use Nette,
	Model, Grid\StoreGrid;


/**
 * Products presenter.
 */
class StorePresenter extends BasePresenter
{
	
	private $storeRepository;
	
	protected function startup(){
		parent::startup();
		$this->storeRepository = $this->context->storeRepository;
	}
	
	public function renderDefault(){
	
	}
	
	public function renderAddStore($idStore){
		
		$this->template->idStore = $idStore;
	}
	
	protected function createComponentStoreGrid(){
		return new StoreGrid($this->context->storeRepository);
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentStoreForm(){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
	
		$form->addText('name', 'Název:')
		->setRequired('Prosím zadejte název dokladu.');
		
		$form->addText('productsAutoComplete', 'Přidat produkty:');
		
		$form->addSubmit('send', 'Uložit doklad')->getControlPrototype()->class('btn btn-success');
	
		if(array_key_exists('idStore', $_GET)){
			$store = $this->storeRepository->findBy(array("id_store" => $_GET["idStore"]))->fetch();
	
			$form->setDefaults($store->toArray());
			$form->addHidden('id_store', $store->id_store);
		}
	
		$form->onSuccess[] = $this->storeFormSucceeded;
		return $form;
	}
	
	public function storeFormSucceeded($form){
		$values = $form->getValues();
		
		if(array_key_exists('idStore', $_GET)){
	
			$update = $this->storeRepository->updateStore($values);
	
			if($update){
				$this->presenter->flashMessage('Doklad byl upraven.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit doklad.', 'error');
			}
		}else{
			
			$values["date"] = new DateTime();
			$values["id_user"] = $this->getUser()->getId();
			
			if($this->storeRepository->createStore($values)){
	
				$this->presenter->flashMessage('Doklad byl uložen.', 'ok');
	
				if($this->isAjax()){
					$this->payload->clearForm = true;
				}else{
					$this->redirect('Store:');
				}
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit doklad.', 'error');
			}
	
		}
	}
	
	public function handleDelete($idStore){
	
		if($this->storeRepository->deleteStore($idStore)){
			$this->presenter->flashMessage("Doklad byl smazán.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat doklad.", "error");
		}
	
	
	}
}
