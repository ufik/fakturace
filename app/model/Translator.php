<?php 

namespace Todo;
use Nette;

class Translator{
	
	private $lang;
	
	public function __construct($lang = 'cs'){
		
		$this->lang = $lang;
	}
	
	public function getTranslations(){
		
		switch ($this->lang){
			case 'en':
				
				$t = array("Dodavatel" => "Supplier",
						"Odběratel" => "Customer",
						"IČ" => "Id",
						"DIČ" => "Vat id",
						"Příjemce" => "Recipient",
						"Platební údaje" => "Payment info",
						"Variabilní symbol" => "Variable symbol",
						"Konstantní symbol" => "Constant symbol",
						"Způsob úhrady" => "Payment",
						"Datum vystavení" => "Date",
						"Datum zdanitelného plnění" => "Del. date",
						"Datum splatnosti" => "Due date",
						"Bankovní spojení" => "Bank",
						"Swift" => "Swift",
						"Iban" => "Iban",
						"Fakturujeme Vám" => "We are invoicing you",
						"Název" => "Name",
						"Počet" => "Count",
						"DPH" => "Vat",
						"Cena bez DPH" => "Amout without vat",
						"Cena s DPH" => "Amount with vat",
						"Cena celkem bez DPH" => "Total without vat",
						"Cena celkem s DPH" => "Total with vat",
						"Razítko a podpis" => "Signature",
						"Celkem" => "Total",
						"Součet DPH" => "Vat",
						"Celkem s DPH" => "Total with vat",
						"Zaokrouhlení" => "Round",
						"Celkem částka k úhradě" => "Total amount",
						"Faktura č." => "Invoice No.",
						"Součet DPH snížená" => "Vat lower",
						"Součet DPH základní" => "Vat basic",
						"Vystavil" => "Created by",
						"Proforma faktura" => "Proforma invoice",
						"Fakturujeme vám zálohu" => "We bill you invoice");
				
			break;
			case 'de':
			break;
				
			default:
				
				$t = array("Dodavatel" => "Dodavatel",
						"Odběratel" => "Odběratel",
						"IČ" => "IČ",
						"DIČ" => "DIČ",
						"Příjemce" => "Příjemce",
						"Platební údaje" => "Platební údaje",
						"Variabilní symbol" => "Variabilní symbol",
						"Konstantní symbol" => "Konstantní symbol",
						"Způsob úhrady" => "Způsob úhrady",
						"Datum vystavení" => "Datum vystavení",
						"Datum zdanitelného plnění" => "Datum zdanitelného plnění",
						"Datum splatnosti" => "Datum splatnosti",
						"Bankovní spojení" => "Bankovní spojení",
						"Swift" => "Swift",
						"Iban" => "Iban",
						"Fakturujeme Vám" => "Fakturujeme Vám",
						"Název" => "Název",
						"Počet" => "Počet",
						"DPH" => "DPH",
						"Cena bez DPH" => "Cena bez DPH",
						"Cena s DPH" => "Cena s DPH",
						"Cena celkem bez DPH" => "Cena celkem bez DPH",
						"Cena celkem s DPH" => "Cena celkem s DPH",
						"Razítko a podpis" => "Razítko a podpis",
						"Celkem" => "Celkem",
						"Součet DPH" => "Součet DPH",
						"Celkem s DPH" => "Celkem s DPH",
						"Zaokrouhlení" => "Zaokrouhlení",
						"Celkem částka k úhradě" => "Celkem částka k úhradě",
						"Faktura č." => "Faktura č.",
						"Součet DPH snížená" => "Součet DPH snížená",
						"Součet DPH základní" => "Součet DPH základní",
						"Vystavil" => "Vystavil",
						"Proforma faktura" => "Proforma faktura",
						"Fakturujeme vám zálohu" => "Fakturujeme Vám zálohu");
				
			break;
		}
		
		return $t;
	}
	
}