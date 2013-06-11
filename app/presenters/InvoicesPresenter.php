<?php

namespace App;

use Nette\Application\Responses\FileResponse;
use Nette\Application\Responses\JsonResponse;

use Nette, Todo,
	Model, Grid\InvoiceGrid;


/**
 * Products presenter.
 */
class InvoicesPresenter extends BasePresenter
{
	
	private $invoiceRepository;
	
	private $contactRepository;
	
	private $settingsRepository;
	
	private $serieRepository;
	
	private $productRepository;
	
	private $invoiceItemsRepository;
	
	protected function startup(){
		parent::startup();
		$this->invoiceRepository = $this->context->invoiceRepository;
		$this->contactRepository = $this->context->contactRepository;
		$this->settingsRepository = $this->context->settingsRepository;
		$this->serieRepository = $this->context->serieRepository;
		$this->productRepository = $this->context->productRepository;
		$this->invoiceItemsRepository = $this->context->invoiceItemsRepository;
	}
	
	public function renderDefault(){
		
	}
	
	public function renderAddInvoice($idInvoice){
		
		$invoice = $this->invoiceRepository->getInvoice($idInvoice);
		
		if(!empty($idInvoice)){			
			$this->template->odberatel = $this->contactRepository->findBy(array("id_contact" => $invoice->odberatel))->fetch();
			$this->template->prijemce = $this->contactRepository->findBy(array("id_contact" => $invoice->prijemce))->fetch();
		}else{
			$this->template->odberatel = false;
			$this->template->prijemce = false;
		}
		
		
		$this->template->invoice = $invoice;
		$this->template->idInvoice = $idInvoice;
	}
	
	protected function createComponentInvoiceGrid(){
		return new InvoiceGrid($this->context->invoiceRepository);
	}
		
	/**
	 * Contact form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentInvoiceForm(){
		$form = new Nette\Application\UI\Form;
	
		$form->getElementPrototype()->class = "ajax";
	
		$form->addText('name', 'Název:')
		->setRequired('Prosím zadejte název faktury.');
		
		$form->addCheckbox('proforma', "Proforma?");
		
		$form->addText('odberatel', "Odběratel");
		$form->addText('prijemce', 'Příjemce');
		
		$series = $this->serieRepository->findAll();
		
		$selectSeries = array();
		foreach($series as $s){
			$selectSeries[$s->id_serie] = $s->name;
		}
		
		if(array_key_exists('idInvoice', $_GET)){
			$form->addText('number', 'Číslo faktury', $selectSeries);
		}else{
			$form->addSelect('series', 'Řada faktury', $selectSeries);
		}
		
		$form->addText('datum_vystaveni', 'Datum vystavení:')->setAttribute("class", "date");
		$form->addText('datum_zdan_plneni', 'Datum zdaň. plnění:')->setAttribute("class", "date");
		$form->addText('datum_splatnosti', 'Datum splatnosti:')->setAttribute("class", "date");
		
		$form->addText('text', 'Fakturujeme Vám:');
		
		//$form->addText('variable_code', 'Variabilní symbol:');
		$form->addText('constant_code', 'Konstantní symbol:');
		$form->addText('payment_type', 'Způsob úhrady:');
		
		$form->addSelect('mena', 'Měna:', array("CZK" => "CZK", "EUR" => "EUR"));
		
		$form->addText('productsAutoComplete', 'Přidat produkty:');
		
		$form->addSubmit('send', 'Uložit fakturu')->getControlPrototype()->class('btn btn-success');
	
		if(array_key_exists('idInvoice', $_GET)){
			$invoice = $this->invoiceRepository->findBy(array("id_invoice" => $_GET["idInvoice"]))->fetch();
	
			$form->setDefaults($invoice->toArray());
			$form->addHidden('id_invoice', $invoice->id_invoice);
		}
	
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->invoiceFormSucceeded;
		return $form;
	}
	
	public function actionGetProducts(){
		
		$products = $this->productRepository->findProductsByName($_GET["term"]);
		
		$returnVals = array();
		foreach($products as $p){
			$returnVals[] = array(
						"label" => $p->name . " (" . $p->catalogue_code . ")",
						"value" => $p->name,
						"id" => $p->id_product,
						"price" => $p->price,
						"mj" => $p->mj
					);
		}
		
		$this->presenter->sendResponse(new JsonResponse($returnVals));
	}
	
	public function actionGetContacts(){
	
		$contacts = $this->contactRepository->findByName($_GET["term"]);
	
		$returnVals = array();
		foreach($contacts as $c){
			$returnVals[] = array(
					"label" => $c->name,
					"id" => $c->id_contact,
					"splatnost" => $c->splatnost
			);
		}
	
		$this->presenter->sendResponse(new JsonResponse($returnVals));
	}
	
	public function invoiceFormSucceeded($form){
		$values = $form->getValues();
		
		// zpracovani produktu z autocomplete
		unset($values["productsAutoComplete"]);

		$values["odberatel"] = $_POST["odberatelid"];
		$values["prijemce"] = $_POST["prijemceid"];

		if(array_key_exists('idInvoice', $_GET)){
	
			$update = $this->invoiceRepository->updateInvoice($values);
			$idInvoice = $_GET["idInvoice"];
			
			$this->presenter->flashMessage('Faktura byla upravena.', 'ok');

		}else{
			
			$serie = $values["series"];
			unset($values["series"]);
			
			$s = $this->serieRepository->findBy(array("id_serie" => $serie))->fetch();
			
			// vytvoreni cisla faktury TODO mely by se locknout tabulky
			$zeroCount = strlen(str_replace($s["prefix"], "", $s["pattern"]));
			$values["number"] = $s["prefix"] . str_pad($s["serie"], $zeroCount, "0", STR_PAD_LEFT);
			
			$this->serieRepository->updateSerie(array("id_serie" => $serie, "serie" => $s["serie"] + 1));
			
			if($idInvoice = $this->invoiceRepository->createInvoice($values)){
	
				$this->presenter->flashMessage('Faktura byla uložena.', 'ok');
	
			}else{
				$this->presenter->flashMessage('Nepodařilo se vytvořit fakturu.', 'error');
			}
	
		}
		
		// smazeme vsechny polozky a nasledne vytvorime nove
		$this->invoiceItemsRepository->deleteItems($idInvoice);
		
		// ulozime prirazene produkty
		if(array_key_exists('products', $_POST)){
			$products = $_POST["products"];
			$names = $_POST["names"];
			$vats = $_POST["dph"];
			$prices = $_POST["prices"];
			$counts = $_POST["counts"];
			$mj = $_POST["mj"];

			for ($i = 0; $i < count($products); $i++) {
				$product = array(
							"id_invoice" => $idInvoice,
							"nazev" => $names[$i],
							"cena" => $prices[$i],
							"dph" => $vats[$i],
							"pocet" => $counts[$i],
							"mj" => $mj[$i]
						);
				
				$this->invoiceItemsRepository->insert($product);
			}
		
		}
		$this->payload->presenter = $this->presenter->getName();
		//$this->redirect("Invoices:");
	}
	
	public function actionGetProductsByIdInvoice($id){
		
		$products = $this->invoiceItemsRepository->getInvoiceItems($id);
	
		$returnVals = array();
		foreach($products as $p){
			$returnVals[] = array(
					"label" => $p->nazev,
					"value" => $p->nazev,
					"id" => $p->id_product,
					"price" => $p->cena,
					"dph" => $p->dph,
					"count" => $p->pocet,
					"mj" => $p->mj
			);
		}
	
		$this->presenter->sendResponse(new JsonResponse($returnVals));
	}
	
	
	protected function createComponentEmailForm(){
		$form = new Nette\Application\UI\Form;
		
		$form->getElementPrototype()->class = "ajax";
		
		$settings = $this->settingsRepository->findAll()->fetchPairs("name", "value");
		$data = $this->invoiceRepository->getInvoice($_GET["idInvoice"]);
		$odberatel = $this->contactRepository->findBy(array("id_contact" => $data['odberatel']))->fetch();
		
		$form->addText('subject', 'Předmět:')
		->setRequired('Prosím vyplňte předmět odeslaného emailu.');
		
		$form->addText('to', 'Komu odeslat:')
			->addRule($form::EMAIL, 'Zadejte validní email.');
		
		$form->addTextArea('message', 'Text zprávy:');
	
		$form->addSubmit('send', 'Odeslat zprávu');
		
		$form->setDefaults(array("to" => $odberatel["email"], "message" => $settings["email_message"]));
		
		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->emailFormSucceeded;
		return $form;
	}
	
	public function emailFormSucceeded($form){
		$values = $form->getValues();
	
		if(array_key_exists('idInvoice', $_GET)){
			
			$pdf = $this->getPdfInvoice($_GET["idInvoice"]);
			
			// odeslani emailu s PDF fakturou
			$savedFile = $pdf->save(WWW_DIR . "/attachments/");
			
			try {
				
				$mail = new Nette\Mail\Message;
				$mail->setFrom("info@holzbecher.net", 'Fakturace Holzbecher');
				$mail->addReplyTo("info@holzbecher.net", 'Fakturace Holzbecher, spol. s r. o. ​barevna a bělidlo Zlíč');
				$mail->addTo($values["to"]);
				$mail->setSubject($values["subject"]);
				$mail->setHtmlBody($values["message"]);
				$mail->addAttachment($savedFile);
				$mail->send();
				
				$this->presenter->flashMessage('Email byl odeslán.', 'ok');
			} catch (Exception $e) {
				$this->presenter->flashMessage('Nepodařilo se odeslat email.', 'error');
			}
		}
		
		if(!$this->presenter->isAjax())
			$this->redirect("Invoices:");
	}
	
	public function renderEmailInvoice($idInvoice){
		
		$this->template->idInvoice = $idInvoice;
	}
	
	public function actionPrintInvoice($idInvoice){
		
		$pdf = $this->getPdfInvoice($idInvoice);
		$pdf->output();
	}
	
	private function getPdfInvoice($idInvoice){
		
		$data = $this->invoiceRepository->getInvoice($idInvoice);
		
		$template = $this->createTemplate()->setFile(APP_DIR . "/templates/Invoices/invoice.latte");
		
		$translator = new Todo\Translator(($data["mena"] != "CZK" ? 'en' : 'cs'));
		
		$template->translator = $translator->getTranslations(); 
		
		$settings = $this->settingsRepository->findAll()->fetchPairs("name", "value");
		$odberatel = $this->contactRepository->findBy(array("id_contact" => $data['odberatel']))->fetch();
		$prijemce = $this->contactRepository->findBy(array("id_contact" => $data['prijemce']))->fetch();
		
		$products = $this->invoiceItemsRepository->getInvoiceItems($idInvoice);
		
		// pokud je mena euro, nepocitame s DPH
		$noDph = false;
		if($data['mena'] == "EUR"){
			$noDph = true;
		}
		
		$total = 0;
		$totalVat = 0;
		$arrayProducts = array();
		$vatBasic = 0;
		$vatLower = 0;
		foreach($products as $p){
			
			$tmp["cena"] = $p->cena;
			$tmp["dph"] = $p->dph;
			$tmp["nazev"] = $p->nazev;
			$tmp["pocet"] = $p->pocet;
			$tmp["mj"] = $p->mj;
			
			if($noDph) $tmp["dph"] = 0;
			
			$tmp["cena_s_dph"] = $tmp["cena"] * ($tmp["dph"] / 100 + 1);
			$tmp["cena_celkem"] = $tmp["cena"] * $tmp["pocet"];
			$tmp["cena_celkem_s_dph"] = $tmp["cena_s_dph"] * $tmp["pocet"];
			
			if($tmp["dph"] == "21"){
				$vatBasic += $tmp["cena_celkem_s_dph"] - $tmp["cena_celkem"];
			}else{
				$vatLower += $tmp["cena_celkem_s_dph"] - $tmp["cena_celkem"];
			}
			
			$total += $tmp["cena"] * $tmp["pocet"];
			$totalVat += $tmp["cena_s_dph"] * $tmp["pocet"];
			
			$arrayProducts[] = $tmp;
		}
		
		$data['international'] = false;
		
		if($data["mena"] == "EUR"){
			$data["mena"] = "&euro;";
			$data['international'] = true;
		}
		else $data["mena"] = " Kč";
		
		if(!$data['international']){
		
			$round = $totalVat - intval($totalVat);
			
			// zaokrouhleni pro koruny
			if($round >= 0.5 || $totalVat < 0){
				if($totalVat < 0){
					$totalVatRounded = $totalVat - (1 - abs($round));
					$sign = "-";
				}
				else{
					$totalVatRounded = $totalVat + (1 - abs($round));
					$sign = "+";
				}
				$round = (1 - abs($round));
				
			}else{
				$totalVatRounded = $totalVat - $round;
				$sign = "-";
			}
		// zaokrouhleni pro eura
		}else{
			
			$round = round(round($totalVat, 1) - $totalVat, 2);

			if($round > 0){
				$totalVatRounded = $totalVat + $round;
				$sign = "+";
			}else{
				$totalVatRounded = $totalVat - $round;
				$sign = "";
			}
		}
		
		$template->noDph = $noDph;
		$template->sign = $sign;
		$template->totalVatRounded = $totalVatRounded;
		$template->round = $round;
		$template->vat = $totalVat - $total;
		$template->vatBasic = $vatBasic;
		$template->vatLower = $vatLower;
		$template->total = $total;
		$template->totalVat = $totalVat;
		$template->products = $arrayProducts;
		$template->settings = $settings;
		$template->data = $data;
		$template->odberatel = $odberatel;
		$template->prijemce = $prijemce;
		
		$pdf = new \PdfResponse($template, $this->context);
		$pdf->documentTitle = "Faktura č. " . $data['number'];
		
		// optional
		//$pdf->documentTitle = date("Y-m-d") . " My super title"; // creates filename 2012-06-30-my-super-title.pdf
		$pdf->pageFormat = "A4"; // wide format
		//$pdf->getMPDF()->setFooter("|© www.mysite.com|"); // footer
		
		return $pdf;
	}
	
	public function handleDelete($idInvoice){
	
		if($this->invoiceRepository->deleteInvoice($idInvoice)){
			$this->presenter->flashMessage("Faktura byla smazána.", "ok");
		}else{
			$this->presenter->flashMessage("Nepodařilo se smazat fakturu.", "error");
		}
	
	
	}
}
