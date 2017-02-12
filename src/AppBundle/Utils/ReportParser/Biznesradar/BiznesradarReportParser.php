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
        
        $urls = array();
        $urls[] = $this->getReportWRUrl();
        $urls[] = $this->getReportBilansUrl();
        $urls[] = $this->getReportRZISUrl();
        
        foreach($urls as $url) {
        	echo $url;
        	//print_r($this->availableReports);
        	$this->parsePage($url);
        }
        	
        print_r($this->reports);
        
        $years = array_keys($this->reports);

        //add company info to parsed reports
        //prepare income value from income parts
        foreach($years as $year) {
        	$this->reports[$year]['identifier'] = new \DateTime($year."-12-31");
        	$this->reports[$year]['company'] = $this->company;
        	if(isset($this->reports[$year]['income_part1'])) {
        		$this->reports[$year]['income'] = $this->reports[$year]['income_part1'];
        	}
        }
        	
        $this->saveReports($this->reports);

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
    	
    	$this->html = $this->getData($url);
    	$dom = new Crawler($this->html);
    	
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
    		
    		$reportDataValue = $tds[$i]->filter('span[class="value"] > span[class="pv"]')
	    		->each(function (Crawler $node, $i) {
	    			$value = $node->text();
	    			return $value;
	    		});
    		//$reportDataValue = $reportDataValue->text();
    		//var_dump($reportDataValue);
    		if(count($reportDataValue)) {
    			$reportDataValue = preg_replace('/\D/', '', $reportDataValue[0]);
    		}
    		else {
    			$reportDataValue = 0;
    		}
    		 
    		if($this->availableReports[$i]['active']) {
    			$this->reports[$this->availableReports[$i]['caption']][$reportDataKey] = $reportDataValue;
    		}
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
   			$year = false;
   			if(strpos($reportsHeads[$i], "/Q") !== false) {
   				$year = $this->extractYearFromQuarterHeader($reportsHeads[$i]);
   			}
   			else {
   				$year = $this->extractYearFromHeader($reportsHeads[$i]);
   			}
   				
   			if($year) {
   				$availableReports[$i] = array('caption' => $year, 'active' => true);
   				echo '+';
   			}
   			else {
   				$availableReports[$i] = array('active' => false);
   				echo '-';
   			}
   		}
   		
   		return $availableReports;
    }
    
    private function extractYearFromHeader($header) {
    	$re = '/\d{4}/';
    	$match = preg_match($re, $header, $matches);
    	if($match) {
    		return $matches[0];
    	}
    	return false;
    }
    
    private function extractYearFromQuarterHeader($header) {
    	$header = preg_replace('/\s+/', '', $header);
    	if(strpos($header, '/Q4') === false) {
    		return false;
    	}
    	$header = str_replace('/Q4', '', $header);
    	echo $header;
    	return $this->extractYearFromHeader($header);
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
    		//'WKCurrent' => 'bookValue_per_share',
    		'BalanceCapital' => 'bookValue',
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