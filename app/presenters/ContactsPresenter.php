<?php

namespace App;

use Nette\Utils\Strings;

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
	
	public function actionImport(){
		$directory = WWW_DIR . "kontakty/";
		
		$inserts = 0;
		
		if($handle = opendir($directory)){
		
			while (false !== ($entry = readdir($handle))) {
				if($entry != "." && $entry != ".."){
					try {
						$vCard = new \vCard($directory . $entry);
						
					if (count($vCard) == 1){
							$contact = $this->parseVCard($vCard);
					}else{
					    foreach ($vCard as $vCardPart)
					    {
				        	$contact = $this->parseVCard($vCardPart);
					    }
					}
					
					$exists = $this->contactRepository->findBy(array("name" => $contact['name']))->fetch();
					
					if(empty($exists)){
						//$this->contactRepository->createContact($contact);
						$ct = $this->transcodeArray($contact);
						print_r($ct);
						$inserts++;
					}
					
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}
			closedir($handle);
		}
		
		$this->template->inserts = $inserts;
	}
	
	private function parseVCard($vCard){
		
		$name = $vCard->n;
		$telephone = $vCard->tel;
		$company = $vCard->org;
		$address = $vCard->adr;
		$email = $vCard->email;
		$ic = "";
		$dic = "";
		
		$return = array(
					"name" => $name[0]['LastName'] . ' ' . $company[0]['Unit1'],
					"ulice" => $address[0]['StreetAddress'],
					"mesto" => $address[0]['Locality'],
					"psc" => $address[0]['PostalCode'],
					"ico" => $ic,
					"dic" => $dic
				);
		
		if(!empty($email)) $return['email'] = $email[0]["Value"];
		if(!empty($telephone)) $return['telefon'] = $telephone[0]["Value"];
		
		return $return;
	}
	
	private function transcodeArray($array){
		
		$rArray = array();
		foreach($array as $key => $value){
			$rArray[$key] = $this->transcode($value);
		}
		
		return $rArray;
	}
	
	private function transcode($string){
		
		return iconv('ASCII', 'UTF-8//IGNORE', $string);
	}
	
	public function handleDelete($idContact){
		
		if($this->contactRepository->deleteContact($idContact)){
			$this->presenter->flashMessage("Kontakt byl smazán.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat kontakt.", "error");
		}
		
		
	}
	
}
