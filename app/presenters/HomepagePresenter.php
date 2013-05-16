<?php

namespace App;

use Nette,
	Model;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter{
	
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
		$this->template->invoices = $this->invoiceRepository->findAll();
		$this->template->contacts = $this->contactRepository->findAll();
		$this->template->series = $this->serieRepository->findAll();
		$this->template->products = $this->productRepository->findAll();
		
		$this->template->invoicesPriceSum = $this->invoiceItemsRepository->getPriceSum();
		
	}

}
