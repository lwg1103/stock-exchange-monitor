<?php

namespace AppBundle\Utils\ReportParser\Bankier;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use Company\Entity\Company;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

class BankierReportParser extends ReportParser {

    public function parse(Company $company) {
        $this->company = $company;
        $this->html = $this->getData();
        
        $this->processParser();
    }
    
    private function processParser() {

        $table = $this->getHtmlTable(new Crawler($this->html));

        $availableQuarters = $this->getAvailableQuarters($table);

        $reportDataTrs = $this->getReportData($table);

        $reports = array();
        foreach($reportDataTrs as $tr) {
        	$tds = $tr->filter('td')->each(function(Crawler $node, $i) {
        		return $node;
        	});

        	$reportDataKey = '';
        	for($i=0; $i<count($tds); $i++) {
        		if($i==0) {
        			$reportDataName = trim($tds[$i]->text());

        			try {
        				$reportDataKey = $this->getReportDataKey($reportDataName);
        			} catch (\Exception $e) {
        				continue 2;
        			}
        		}
        		else {
        			$reportDataValue = $tds[$i]->text();
        			$reportDataValue = preg_replace('/\D/', '', $reportDataValue);
        			$reports[$availableQuarters[$i]][$reportDataKey] = $reportDataValue;
        		}
        	}
        }

        
        $this->saveReports($reports);


        //print_r($reportData);

        die;

        $data = array();
        $data['company'] = $company;
        $data['identifier'] = new \DateTime('2015-06-30');
        $data['income'] = '12';
        $data['netProfit'] = '13';
        $data['operationalNetProfit'] = '';
        $data['bookValue'] = '15';
        $data['sharesQuantity'] = '16';
        $data['assets'] = '17';
        //$report = $this->prepareReport($data);
        //$this->em->persist($report);
        //$this->em->flush();
    }
    
    private function saveReports($reports) {
    	
    }

    private function getHtmlTable(Crawler $dom) {
    	$header = null;
    	$headers = $dom->filter('div[class="boxHeader"] > h2')
    		->each(function(Crawler $node, $i) {
    			if($node->text() == "Skonsolidowane raporty kwartalne") {
	    			return $node;
	    		}
	    		return null;
	    	});

    	foreach($headers as $h) {
    		if($h != null) {
    			$header = $h;
    		}
    	}

   		if($header == null) {
   			throw new Exception('nie uda?o si?pobra?danych');
   		}

   		$ths = array();
   		$table = $header->parents()->filter('div[class="boxContent boxTable"] > table');

   		return $table;
    }

    private function getAvailableQuarters($table) {
    	$quartersHeads = $table->filter('thead th')
    		->each(function (Crawler $node, $i) {
	    		$quarterElement = $node->filter('strong');
	    		$quarterName = '';
	    		if($quarterElement && count($quarterElement)) {
	    			$quarterName = $quarterElement->text();
	    		}
	    		return $quarterName;
	    	});

    	$availableQuarters = array();

   		for($i=0; $i < count($quartersHeads); $i++) {
   			$re = '/[I|V]+ Q \d{4}/';
   			$match = preg_match($re, $quartersHeads[$i], $matches);
   			if($match) {
   				$availableQuarters[$i] = $quartersHeads[$i];
   			}
   		}

   		return $availableQuarters;
    }

    private function getReportData($table) {
    	$reportData = $table->filter('tbody tr')->each(function (Crawler $node, $i) {
    		return $node;
    	});

    	return $reportData;
    }

    private function getReportDataKey($reportDataName) {

    	$translation = array(
    		'Zysk (strata) netto (tys. zł)*' => 'netProfit',
    	);
    	if(!array_key_exists($reportDataName, $translation)) {
    		throw new \Exception('nieznana dana');
    	}
    	return $translation[$reportDataName];
    }


    private function getReportUrl() {
    	return "http://www.bankier.pl/gielda/notowania/akcje/PZU"."/wyniki-finansowe";
        return "http://www.bankier.pl/gielda/notowania/akcje/".$this->company->getMarketId()."/wyniki-finansowe";
    }

    private function getData() {

        try {
            $html = file_get_contents($this->getReportUrl());
        } catch (ContextErrorException $e) {
            throw new Exception('nie uda?o si?pobra?danych');
        }

        return $html;
    }
}