<?php

namespace App;

use Nette,
	Model;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter{
	/**
	 *
	 * @var type 
	 */
	private $invoiceRepository;
	
	/**
	 *
	 * @var type 
	 */
	private $contactRepository;
	
	/**
	 *
	 * @var type 
	 */
	private $settingsRepository;
	
	/**
	 *
	 * @var type 
	 */
	private $serieRepository;
	
	/**
	 *
	 * @var type 
	 */
	private $productRepository;
	
	/**
	 *
	 * @var type 
	 */
	private $invoiceItemsRepository;
	
	private $settings = null;
	
	/* */
	protected function startup(){
		parent::startup();
		$this->invoiceRepository = $this->context->invoiceRepository;
		$this->contactRepository = $this->context->contactRepository;
		$this->settingsRepository = $this->context->settingsRepository;
		$this->serieRepository = $this->context->serieRepository;
		$this->productRepository = $this->context->productRepository;
		$this->invoiceItemsRepository = $this->context->invoiceItemsRepository;
	}
	
	/*  */
	public function renderDefault(){
		$this->template->contacts = $this->contactRepository->findAll();
		$this->template->series = $this->serieRepository->findAll();
		$this->template->products = $this->productRepository->findAll();
		
		$this->template->invoicesPriceSum = $this->invoiceItemsRepository->getPriceSum();
		
		$this->settings = $this->settingsRepository->findAll()->fetchPairs("name", "value");
		
		$invoices = array();
		// we get last six months stats of invoices
		for ($i = 0; $i <= 5; $i++) {
		    $invoices[$i] = $this->__getInvoicesByMonth($i);
		}
		
		$totalCZE = $this->invoiceRepository->getInvoicesByCurrency('CZK');
		$totalEUR = $this->invoiceRepository->getInvoicesByCurrency('EUR');
		
		$sums['total']['CZE'] = $this->__countInvoicesSum($totalCZE);
		$sums['total']['EUR'] = $this->__countInvoicesSum($totalEUR);
		$sums['total']['EURc'] = $this->__countInvoicesSum($totalEUR, $this->settings['course']);
		
		$this->template->invoicesSum = $invoices;
		$this->template->settings = $this->settings;
		$this->template->czeCount = count($totalCZE);
		$this->template->eurCount = count($totalEUR);
		$this->template->sums = $sums;
	}
	
	/**
	 * TODO rename
	 * @param type $invoices
	 * @param type $course
	 * @return type
	 */
	private function __countInvoicesSum($invoices, $course = null){
	    
	    $sum = 0;
	    foreach($invoices as $item){
		foreach($item->related('invoiceItems', 'id_invoice') as $i){
		    $sum += $i->cena * $i->pocet;
		}
	    }
	    
	    if($course != null) $sum *= $course;
	    
	    return $sum;
	}
	
	/**
	 * 
	 * @param type $month
	 * @return type
	 */
	private function __getInvoicesByMonth($monthsAgo = 0){
	    
	    $monthsAgoString = "-$monthsAgo month";
	    $invoices = array();
	    
	    $invoices["label"] = date("F", strtotime($monthsAgoString));
	    $invoices["CZE"] = $this->__countInvoicesSum($this->invoiceRepository->getInvoicesByCurrency('CZK', date("Y-m-1", strtotime($monthsAgoString)), date("Y-m-t", strtotime($monthsAgoString))));
	    $invoices["EUR"] = $this->__countInvoicesSum($this->invoiceRepository->getInvoicesByCurrency('EUR', date("Y-m-1", strtotime($monthsAgoString)), date("Y-m-t", strtotime($monthsAgoString))));
	    $invoices["EURc"] = $this->__countInvoicesSum($this->invoiceRepository->getInvoicesByCurrency('EUR', date("Y-m-1", strtotime($monthsAgoString)), date("Y-m-t", strtotime($monthsAgoString))), $this->settings['course']);
	    
	    return $invoices;
	}

}
