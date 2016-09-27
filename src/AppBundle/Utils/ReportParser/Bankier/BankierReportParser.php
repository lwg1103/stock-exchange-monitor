<?php

namespace AppBundle\Utils\ReportParser\Bankier;
 
use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use AppBundle\Entity\Company;

class BankierReportParser extends ReportParser {
	
	public function parse(Company $company) {
		$this->company = $company;
		$html = $this->getData();
	}
	
	private function getReportUrl() {
		return "http://www.bankier.pl/gielda/notowania/akcje/".$this->company->getMarketId()."/wyniki-finansowe";
	}
	
	private function getData() {
		
		try {
			$html = file_get_contents($this->getReportUrl());
		} catch (ContextErrorException $e) {
			$html = '';
		}
		
		return $html;
	}
}