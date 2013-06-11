<?php

namespace App;

use Nette\Utils\Strings;

use Nette\DateTime;
use Nette\Application\Responses\JsonResponse;
use Nette, Todo, Model\Authenticator,
	Model, Grid\StoreGrid;


/**
 * Products presenter.
 */
class UsersPresenter extends BasePresenter
{
	
	private $storeRepository;
	
	private $storeItemsRepository;
	
	private $contactRepository;
	
	private $serieRepository;
	
	private $settingsRepository;
	
	private $userRepository;
	
	protected function startup(){
		parent::startup();
		$this->storeRepository = $this->context->storeRepository;
		$this->storeItemsRepository = $this->context->storeItemsRepository;
		$this->contactRepository = $this->context->contactRepository;
		$this->serieRepository = $this->context->serieRepository;
		$this->settingsRepository = $this->context->settingsRepository;
		$this->userRepository = $this->context->userRepository;
	}
	
	public function renderDefault(){
	
	}
	
	public function renderAddStore($idStore){
		
		$store = $this->storeRepository->getStore($idStore);
		
		if(!empty($idStore)){
			$this->template->odberatel = $this->contactRepository->findBy(array("id_contact" => $store->odberatel))->fetch();
		}else{
			$this->template->odberatel = false;
		}
		
		
		$this->template->store = $store;
		$this->template->idStore = $idStore;
	}
	
	public function renderDetail($idUser){

	}
	
	protected function createComponentStoreGrid(){
		return new StoreGrid($this->context->storeRepository);
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentUserForm($idUser){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
	
		$form->addText('name', 'Jméno:')
		->setRequired('Prosím zadejte Vaše jméno.');
		
		$form->addText('email', 'Email:');
		
		$form->addText('phone', 'Telefon:');
		
		$form->addPassword('pass', 'Změnit heslo:')->getControlPrototype()->placeholder("Minimálně 6 znaků.");
		$form->addPassword('passCheck', 'Heslo znovu:')->getControlPrototype()->placeholder("Opište heslo znovu.");
		
		$form->addSubmit('send', 'Uložit nastavení')->getControlPrototype()->class('btn btn-success');
		
		if(is_numeric($this->presenter->param["id"])){
			
			$user = $this->userRepository->findBy(array("id" => $this->presenter->param["id"]))->fetch();
	
			$form->setDefaults($user->toArray());
			$form->addHidden('idUser', $this->presenter->param["id"]);
		}
	
		$form->onSuccess[] = $this->userFormSucceeded;
		return $form;
	}
	
	public function userFormSucceeded($form){
		$values = $form->getValues();

		if(is_numeric($_REQUEST["idUser"])){
			
			$password1 = $values["pass"];
			$password2 = $values["passCheck"];
			
			unset($values["pass"]);
			unset($values["passCheck"]);
			
			if(!empty($password1) && !empty($password2) && $password1 == $password2 && Strings::length($password1) >= 6){
				
				$values["password"] = Authenticator::calculateHash($password1);
				$this->presenter->flashMessage('Heslo upraveno.', 'ok');
			}
			
			$values["id"] = $_REQUEST["idUser"];
			$update = $this->userRepository->updateUser($values);
	
			if($update){
				$this->presenter->flashMessage('Uživatel byl upraven.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit uživatele.', 'error');
			}
		}else{
			
			
			if($idStore = $this->storeRepository->createStore($values)){
	
				$this->presenter->flashMessage('Uživatel byl uložen.', 'ok');
	
				if($this->isAjax()){
					$this->payload->clearForm = true;
				}else{
					$this->redirect('Users:');
				}
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit uživatele.', 'error');
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
