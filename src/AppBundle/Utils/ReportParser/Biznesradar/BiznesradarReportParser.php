<?php

namespace AppBundle\Utils\ReportParser\Biznesradar;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use AppBundle\Utils\ReportParser\InvalidCompanyTypeException;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Symfony\Component\DomCrawler\Crawler;


class BiznesradarReportParser extends ReportParser {
	
	var $reports = array();
	var $availableReports = array();

    public function parse(Company $company) {
    	$this->reset();
        $this->company = $company;
        
        $this->checkCompany($company);
        
        $urls = array();
        $urls[] = $this->getReportWRUrl();
        $urls[] = $this->getReportBilansUrl();
        $urls[] = $this->getReportRZISUrl();
        
        foreach($urls as $url) {
        	echo $url;
        	$this->parsePage($url);
        }
        
        $years = array_keys($this->reports);

        //add company info to parsed reports
        //prepare income value from income parts
        foreach($years as $year) {
        	$this->reports[$year]['identifier'] = new \DateTime($year."-12-31");
        	$this->reports[$year]['company'] = $this->company;
        	if(isset($this->reports[$year]['income_part1'])) {
        		$this->reports[$year]['income'] = $this->reports[$year]['income_part1'];
        	}
        	
        	if($this->company->getType() == Type::BANK) {
        		$this->reports[$year]['income'] = 
        			$this->reports[$year]['income_part1_bank'] + $this->reports[$year]['income_part2_bank'];
        		$this->reports[$year]['operationalNetProfit'] = $this->reports[$year]['operationalNetProfit_bank'];
        		$this->reports[$year]['bookValue'] = $this->reports[$year]['bookValue_bank'];
        		$this->reports[$year]['currentAssets'] = 0;//$this->reports[$year]['bookValue_bank'];
        		$this->reports[$year]['currentLiabilities'] = 0;//$this->reports[$year]['bookValue_bank'];
        	}
        }
        	
        $this->saveReports($this->reports);
    }
    
    private function reset() {
    	$this->reports = array();
    	$this->availableReports= array();
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
    			continue;
    		}
    	}
    }
    
    private function parseRow($tr) {
    	$tds = $tr->filter('td[class="h"]')->each(function(Crawler $node, $i) {
    		return $node;
    	});
    	
    	$tdsNewest = $tr->filter('td[class="h"]')->each(function(Crawler $node, $i) {
    		return $node;
    	});
    	
    	if(count($tdsNewest)) {
    		$tds[] = $tdsNewest[count($tdsNewest) - 1];
    	}
    			 
    	
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
    		
    	$reportsHeadsNewest = $table->filter('th[class="thq h newest"]')
    		->each(function (Crawler $node, $i) {
    			$reportName = $node->text();
    			 
    			return $reportName;
    		});
    		
    	if(count($reportsHeadsNewest)) {
    		$reportsHeads[] = $reportsHeadsNewest[count($reportsHeadsNewest) - 1];
    	}

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
    		'BalanceTotalCapital' => 'bookValue_bank',
    		'IncomeEBIT' => 'operationalNetProfit',
    		'IncomeNetOtherOperatingIncome' => 'operationalNetProfit_bank',
    		'IncomeRevenues' => 'income_part1',
    		'IncomeOtherOperatingIncome' => 'income_part2',
    		'IncomeFinanceIncome' => 'income_part3',
    		'IncomeFeeIncome' => 'income_part1_bank',
    		'IncomeIntrestIncome' => 'income_part2_bank'
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
    
    private function checkCompany() {
    	if($this->company->getType() != Type::BANK && $this->company->getType() != Type::ORDINARY) {
    		throw new InvalidCompanyTypeException();
    	}
    }
}