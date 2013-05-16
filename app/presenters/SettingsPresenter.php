<?php

namespace App;

use Nette,
	Model;


/**
 * Products presenter.
 */
class SettingsPresenter extends BasePresenter
{
	
	private $settingsRepository;
	
	protected function startup(){
		parent::startup();
		$this->settingsRepository = $this->context->settingsRepository;
	}
	
	public function renderDefault(){
		
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSettingsForm(){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
		
		$form->addGroup("Dodavatel - fakturace");
		$form->addText('invoice_company', 'Jméno společnosti:');
		$form->addText('invoice_street', 'Ulice:');
		$form->addText('invoice_psc', 'PSČ:');
		$form->addText('invoice_city', 'Město:');
		$form->addText('invoice_ic', 'IČ:');
		$form->addText('invoice_dic', 'DIČ:');
		$form->addText('invoice_registration', 'Registrace:');
		
		$form->addGroup("Emailové nastavení");
		$form->addTextArea('email_message', 'Text emailu:');
		
		$form->addGroup("Bankovní spojení");
		$form->addText('bank_account_number', 'Číslo účtu:');
		$form->addText('bank_account_number_int', 'Číslo účtu zahraničí:');
		$form->addText('bank_swift', 'SWIFT:');
		$form->addText('bank_iban', 'IBAN:');
		$form->addText('bank_name', 'Banka:');
		$form->addText('bank_name_int', 'Banka zahraničí:');
		$form->addText('bank_iban_int', 'IBAN zahraničí:');
		
		//$form->addGroup("Systemové nastavení");
		
		$form->addSubmit('send', 'Uložit nastavení')->getControlPrototype()->class('btn btn-success');
	
		$settings = $this->settingsRepository->findAll();
		
		$defaults = array();
		foreach($settings as $s){
			$defaults[$s->name] = $s->value;
		}
		
		$form->setDefaults($defaults);
	
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->settingsFormSucceeded;
		return $form;
	}
	
	public function settingsFormSucceeded($form){
		$values = $form->getValues();
		
		foreach($values as $key => $v){
			$exists = $this->settingsRepository->findBy(array("name" => $key))->fetch();
			
			if(empty($exists)){
				$this->settingsRepository->insert($key, $v);
			}
			else{
				$this->settingsRepository->update($key, $v);
			}
		}
		
		$this->presenter->flashMessage('Nastavení bylo uloženo.', 'ok');
	}
}
