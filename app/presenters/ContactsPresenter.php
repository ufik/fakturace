<?php

namespace App;

use Nette\ObjectMixin;

use Nette,
	Model, Grid\ContactGrid;


/**
 * Contacts presenter.
 */
class ContactsPresenter extends BasePresenter{
	
	private $contactRepository;
	
	protected function startup(){
		parent::startup();
		$this->contactRepository = $this->context->contactRepository;
	}
	
	public function renderDefault(){
		
	}
	
	public function renderAddContact($idContact){
		
	}
	
	protected function createComponentContactGrid(){
		return new ContactGrid($this->context->contactRepository);
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentContactForm(){
		$form = new Nette\Application\UI\Form;
		
		$form->getElementPrototype()->class = "ajax";
		
		$form->addText('name', 'Název:')
		->setRequired('Prosím zadejte jméno společnosti.');
		
		$form->addText('telefon', 'Telefon:');
		
		$form->addText('email', 'E-mail:');
		
		$form->addText('ico', 'IČO:');
		
		$form->addText('dic', 'DIČ:');
		
		$form->addText('ulice', 'Ulice:');
		
		$form->addText('psc', 'PSČ:');
		
		$form->addText('mesto', 'Město:');

		$form->addSubmit('send', 'Uložit kontakt');
		
		if(array_key_exists('idContact', $_GET)){
			$contact = $this->contactRepository->findBy(array("id_contact" => $_GET["idContact"]))->fetch();
		
			$form->setDefaults($contact->toArray());
			$form->addHidden('id_contact', $contact->id_contact);
		}
		
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->contactFormSucceeded;
		return $form;
	}
	
	public function contactFormSucceeded($form){
		$values = $form->getValues();
		
		if(array_key_exists('idContact', $_GET)){
			
			$update = $this->contactRepository->updateContact($values);
			
			if($update){
				$this->presenter->flashMessage('Kontakt byl upraven.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit kontakt.', 'error');
			}
		}else{

			if($this->contactRepository->createContact($values)){
			
				$this->presenter->flashMessage('Kontakt byl uložen.', 'ok');
			
				if($this->isAjax()){
					$this->payload->clearForm = true;
				}else{
					$this->redirect('Contacts:');
				}
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit kontakt.', 'error');
			}
			
		}
	}
	
	public function handleDelete($idContact){
		
		if($this->contactRepository->deleteContact($idContact)){
			$this->presenter->flashMessage("Kontakt byl smazán.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat kontakt.", "error");
		}
		
		
	}
	
}
