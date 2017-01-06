<?php

namespace AppBundle\Utils\ReportParser\Biznesradar;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use Company\Entity\Company;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

class BiznesradarReportParser extends ReportParser {
	
	var $reports = array();
	var $availableReports = array();

    public function parse(Company $company) {
        $this->company = $company;
        
        /*$urlRZIS = $this->getReportRZISUrl();
        $urlBilans = $this->getReportBilansUrl();
        $urlWR = $this->getReportWRUrl();*/
        $urls = array();
        //$urls[] = $this->getReportWRUrl();
        //$urls[] = $this->getReportBilansUrl();
        $urls[] = $this->getReportRZISUrl();
        
        foreach($urls as $url) {
        	$this->parsePage($url);
        }
        	
        print_r($this->reports);
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
        $data['currentAssets'] = '17';
        $data['liabilities'] = '17';
        $data['currentLiabilities'] = '17';
        //$report = $this->prepareReport($data);
        //$this->em->persist($report);
        //$this->em->flush();
    }
    
    private function parsePage($url) {
    	$html = $this->getData($url);
    	//$dom = \Phpsimpledom\str_get_html($html);
    	$cssConverter = new CssSelectorConverter();
    	//$path = $cssConverter->toXPath('.box615');
    	$dom = new Crawler($html);
    	
    	$table = $this->getHtmlTable($dom);
    	
    	$this->availableReports = $this->getAvailableReports($table);
    	
    	$reportDataTrs = $this->getReportData($table);
    	
    	foreach($reportDataTrs as $tr) {
    		try {
    			$this->parseRow($tr);
    		} 
    		catch (\Exception $e) {
    			echo 'e';
    			continue;
    		}
    	}
    }
    
    private function parseRow($tr) {
    	$tds = $tr->filter('td[class="h"]')->each(function(Crawler $node, $i) {
    		return $node;
    	});
    	
    	$reportDataKey = $this->getReportDataKey($tr->attr("data-field"));
    	
    	for($i=0; $i<count($tds); $i++) {
    		if(count($this->availableReports) <= $i) {
    			continue;
    		}
    		echo 'i';
    		$reportDataValue = $tds[$i]->filter('span[class="value"] > span[class="pv"]')
	    		->each(function (Crawler $node, $i) {
	    			$value = $node->text();
	    			return $value;
	    		});
    		//$reportDataValue = $reportDataValue->text();
    		var_dump($reportDataValue);
    		if(count($reportDataValue)) {
    			$reportDataValue = preg_replace('/\D/', '', $reportDataValue[0]);
    		}
    		else {
    			$reportDataValue = 0;
    		}
    		 
    		$this->reports[$this->availableReports[$i]][$reportDataKey] = $reportDataValue;
    	}
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
    		'BalanceCurrentAssets' => 'currentAssets',
    		'BalanceTotalAssets' => 'assets',
    		'BalanceCurrentLiabilities' => 'currentLiabilities',
    		'BalanceTotalLiabilities' => 'liabilities',
    		'ShareAmountCurrent' => 'sharesQuantity',
    		'WKCurrent' => 'bookValue_per_share',
    		'IncomeEBIT' => 'operationalNetProfit',
    		'IncomeRevenues' => 'income_part1',
    		'IncomeOtherOperatingIncome' => 'income_part2',
    		'IncomeFinanceIncome' => 'income_part3'
    	);
    	
    	if(!array_key_exists($reportDataName, $translation)) {
    		throw new \Exception('nieznana dana');
    	}
    	
    	return $translation[$reportDataName];
    }

    private function getReportRZISUrl() {
		return "http://www.biznesradar.pl/raporty-finansowe-rachunek-zyskow-i-strat/".$this->company->getMarketId();
    }
    
    private function getReportBilansUrl() {
    	return "http://www.biznesradar.pl/raporty-finansowe-bilans/".$this->company->getMarketId();
    }
    
    private function getReportWRUrl() {
    	return "http://www.biznesradar.pl/wskazniki-wartosci-rynkowej/".$this->company->getMarketId();
    }
    
    private function getData($url) {

        try {
            $html = file_get_contents($url);
        } catch (ContextErrorException $e) {
            throw new Exception('nie udało się pobrać danych');
        }

        return $html;
    }
}