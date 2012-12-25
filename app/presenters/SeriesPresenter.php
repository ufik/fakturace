<?php

namespace App;

use Nette,
	Model, Grid\SerieGrid;


/**
 * Products presenter.
 */
class SeriesPresenter extends BasePresenter
{
	
	private $serieRepository;
	
	protected function startup(){
		parent::startup();
		$this->serieRepository = $this->context->serieRepository;
	}
	
	public function renderDefault(){
		
	}
	
	public function renderAddSerie($idSerie){}
	
	protected function createComponentSerieGrid(){
		return new SerieGrid($this->context->serieRepository);
	}
	
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSerieForm(){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
	
		$form->addText('name', 'Název:')
		->setRequired('Prosím zadejte název serie.');
	
		$form->addText('serie', 'Hodnota řady:');
		
		$form->addText('prefix', 'Prefix:');
		
		$form->addText('pattern', 'Vzor řady:');
	
		$form->addSubmit('send', 'Uložit řadu');
	
		if(array_key_exists('idSerie', $_GET)){
			$serie = $this->serieRepository->findBy(array("id_serie" => $_GET["idSerie"]))->fetch();
	
			$form->setDefaults($serie->toArray());
			$form->addHidden('id_serie', $serie->id_serie);
		}
	
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->serieFormSucceeded;
		return $form;
	}
	
	public function serieFormSucceeded($form){
		$values = $form->getValues();
	
		if(array_key_exists('idSerie', $_GET)){
	
			$update = $this->serieRepository->updateSerie($values);
	
			if($update){
				$this->presenter->flashMessage('Řada byla upravena.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit řadu.', 'error');
			}
		}else{
	
			if($this->serieRepository->createSerie($values)){
	
				$this->presenter->flashMessage('Řada byla uložena.', 'ok');
	
				if($this->isAjax()){
					$this->payload->clearForm = true;
				}else{
					$this->redirect('Series:');
				}
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit řadu.', 'error');
			}
	
		}
	}
	
	public function handleDelete($idSerie){
	
		if($this->serieRepository->deleteSerie($idSerie)){
			$this->presenter->flashMessage("Řada byla smazána.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat řadu.", "error");
		}
	
	
	}
}
