<?php

namespace App;

use Nette\DateTime;
use Nette\Application\Responses\JsonResponse;
use Nette, Todo,
	Model, Grid\StoreGrid;


/**
 * Products presenter.
 */
class StorePresenter extends BasePresenter
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
		->setRequired('Prosím zadejte název dokladu.')
		->getControlPrototype()->placeholder("Název");
		
		$series = $this->serieRepository->findAll();
		
		$selectSeries = array();
		foreach($series as $s){
			$selectSeries[$s->id_serie] = $s->name;
		}
		
		if(array_key_exists('idStore', $_GET)){
			$form->addText('number', 'Číslo dokladu', $selectSeries);
		}else{
			$form->addSelect('series', 'Řada dokladu', $selectSeries);
		}
		
		$form->addText('odberatel', "Odběratel")->getControlPrototype()->placeholder("Odběratel");
		$form->addText('productsAutoComplete', 'Přidat produkty:')->getControlPrototype()->placeholder("Přidat produkty:");
		
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
		
		// zpracovani produktu z autocomplete
		unset($values["productsAutoComplete"]);
		
		$values["odberatel"] = $_POST["odberatelid"];
		
		if(array_key_exists('idStore', $_GET)){
			
			$idStore = $_GET['idStore'];
			$update = $this->storeRepository->updateStore($values);
	
			if($update){
				$this->presenter->flashMessage('Doklad byl upraven.', 'ok');
			}else{
				$this->presenter->flashMessage('Nepodařilo se upravit doklad.', 'error');
			}
		}else{
			
			$values["date"] = new DateTime();
			$values["id_user"] = $this->getUser()->getId();
			
			$serie = $values["series"];
			unset($values["series"]);
			
			$s = $this->serieRepository->findBy(array("id_serie" => $serie))->fetch();
				
			// vytvoreni cisla faktury TODO mely by se locknout tabulky
			$zeroCount = strlen(str_replace($s["prefix"], "", $s["pattern"]));
			$values["number"] = $s["prefix"] . str_pad($s["serie"], $zeroCount, "0", STR_PAD_LEFT);
				
			$this->serieRepository->updateSerie(array("id_serie" => $serie, "serie" => $s["serie"] + 1));
			
			if($idStore = $this->storeRepository->createStore($values)){
	
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
		
		// smazeme vsechny polozky a nasledne vytvorime nove
		$this->storeItemsRepository->deleteItems($idStore);
		
		// ulozime prirazene produkty
		if(array_key_exists('products', $_POST)){
			$products = $_POST["products"];
			$names = $_POST["names"];
			$counts = $_POST["counts"];
			$mj = $_POST["mj"];
		
			for ($i = 0; $i < count($products); $i++) {
				$product = array(
						"id_store" => $idStore,
						"name" => $names[$i],
						"count" => $counts[$i],
						"mj" => $mj[$i],
						"id_product" => $products[$i]
				);
		
				$this->storeItemsRepository->insert($product);
			}
		}
	}
	
	public function actionGetProductsByIdStore($id){
	
		$products = $this->storeItemsRepository->getItems($id);
	
		$returnVals = array();
		foreach($products as $p){
			$returnVals[] = array(
					"label" => $p->name,
					"value" => $p->name,
					"id" => $p->id_product,
					"count" => $p->count,
					"mj" => $p->mj
			);
		}
	
		$this->presenter->sendResponse(new JsonResponse($returnVals));
	}
	
	public function actionPrintStore($idStore){
	
		$pdf = $this->getPdfStore($idStore);
		$pdf->output();
	}
	
	private function getPdfStore($idStore){
	
		$data = $this->storeRepository->getStore($idStore);
	
		$template = $this->createTemplate()->setFile(APP_DIR . "/templates/Store/doklad.latte");
		
		$translator = new Todo\Translator();
		
		$template->translator = $translator->getTranslations();
	
		$settings = $this->settingsRepository->findAll()->fetchPairs("name", "value");
		$odberatel = $this->contactRepository->findBy(array("id_contact" => $data['odberatel']))->fetch();
	
		$products = $this->storeItemsRepository->getItems($idStore);
		
		foreach($products as $p){
				
			$tmp["nazev"] = $p->name;
			$tmp["pocet"] = $p->count;
			$tmp["mj"] = $p->mj;
				
			$arrayProducts[] = $tmp;
		}
	
		$template->user = $this->userRepository->getUser($data["id_user"]);
		$template->products = $products;
		$template->settings = $settings;
		$template->data = $data;
		$template->odberatel = $odberatel;
	
		$pdf = new \PdfResponse($template, $this->context);
		$pdf->documentTitle = "Faktura č. " . $data['number'];
	
		// optional
		//$pdf->documentTitle = date("Y-m-d") . " My super title"; // creates filename 2012-06-30-my-super-title.pdf
		$pdf->pageFormat = "A4"; // wide format
		//$pdf->getMPDF()->setFooter("|© www.mysite.com|"); // footer
	
		return $pdf;
	}
	
	public function handleDelete($idStore){
	
		if($this->storeRepository->deleteStore($idStore)){
			$this->presenter->flashMessage("Doklad byl smazán.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat doklad.", "error");
		}
	
	
	}
}
