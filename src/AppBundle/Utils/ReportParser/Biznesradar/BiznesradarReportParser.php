<?php

namespace AppBundle\Utils\ReportParser\Biznesradar;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use Company\Entity\Company;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

class BiznesradarReportParser extends ReportParser {

    public function parse(Company $company) {
        $this->company = $company;
        $html = $this->getData();
        //$dom = \Phpsimpledom\str_get_html($html);
        $cssConverter = new CssSelectorConverter();
        //$path = $cssConverter->toXPath('.box615');
        $dom = new Crawler($html);

        $table = $this->getHtmlTable($dom);

        $availableReports = $this->getAvailableReports($table);

        $reportDataTrs = $this->getReportData($table);
        

        $reports = array();
        foreach($reportDataTrs as $tr) {
        	$tds = $tr->filter('td')->each(function(Crawler $node, $i) {
        		return $node;
        	});
        	try {
        		$reportDataKey = $this->getReportDataKey($tr->attr("data-field"));
        	} catch (\Exception $e) {
        		continue;
        	}
        	
        	for($i=0; $i<count($tds); $i++) {
        		if(count($availableReports) <= $i) {
        			continue;
        		}
        		$reportDataValue = $tds[$i]->text();
        		$reportDataValue = preg_replace('/\D/', '', $reportDataValue);
        		$reports[$availableReports[$i]][$reportDataKey] = $reportDataValue;
        		
        	}
        }

        print_r($reports);
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

    private function getHtmlTable(Crawler $dom) {
    	$header = null;
    	$table = $dom->filter('table[class="report-table"]');

    	return $table;
    }

    private function getAvailableReports($table) {
    	$reportsHeads = $table->filter('th[class="thq h"]')
    		->each(function (Crawler $node, $i) {
	    		$reportName = $node->text();
	    		
	    		return $reportName;
	    	});

    	$availableReports = array();

   		for($i=0; $i < count($reportsHeads); $i++) {
   			$re = '/\d{4}/';
   			$match = preg_match($re, $reportsHeads[$i], $matches);
   			if($match) {
   				$availableReports[$i] = $reportsHeads[$i];
   			}
   		}

   		return $availableReports;
    }

    private function getReportData($table) {
    	$reportData = $table->filter('tr')->each(function (Crawler $node, $i) {
    		return $node;
    	});

    	return $reportData;
    }

    private function getReportDataKey($reportDataName) {
    	
    	$translation = array(
    		'IncomeNetProfit' => 'netProfit',
    	);
    	if(!array_key_exists($reportDataName, $translation)) {
    		throw new \Exception('nieznana dana');
    	}
    	return $translation[$reportDataName];
    }

    private function getReportUrl() {
    	return "http://www.biznesradar.pl/raporty-finansowe-rachunek-zyskow-i-strat/PKO";
        return "http://www.biznesradar.pl/raporty-finansowe-rachunek-zyskow-i-strat/".$this->company->getMarketId();
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