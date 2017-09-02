<?php
namespace Report\Parser\Biznesradar;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Carbon\Carbon;
use Report\Parser;
use Report\ParserInterface;
use Report\Parser\InvalidCompanyTypeException;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Symfony\Component\DomCrawler\Crawler;

class BiznesradarQuarterlyParser extends BiznesradarParser implements ParserInterface
{
    const REPORT_HEADER_FIRST_QUARTER = "/Q1";
    const REPORT_HEADER_SECOND_QUARTER = "/Q2";
    const REPORT_HEADER_THIRD_QUARTER = "/Q3";
    const REPORT_HEADER_FOURTH_QUARTER = "/Q4";
    
    protected function getAvailableReports($table)
    {
        $reportsHeads = $table->filter('th[class="thq h"]')->each(function (Crawler $node, $i) {
            $reportName = $node->text();
            
            return $reportName;
        });
        
        $reportsHeadsNewest = $table->filter('th[class="thq h newest"]')->each(function (Crawler $node, $i) {
            $reportName = $node->text();
            
            return $reportName;
        });
        
        if (count($reportsHeadsNewest)) {
            $reportsHeads[] = $reportsHeadsNewest[count($reportsHeadsNewest) - 1];
        }
        
        $this->log('reports table heads: ' . print_r($reportsHeads, true));
        
        $availableReports = array();
        
        for ($i = 0; $i < count($reportsHeads); $i ++) {
            $this->log('checking head: ' . $reportsHeads[$i]);
            
            $year = $this->extractYearFromQuarterHeader($reportsHeads[$i]);
            $quarter = $this->extractQuarterFromHeader($reportsHeads[$i]); 
            
            
            if ($year && $quarter) {
                $this->log('quarter head: ' . $year."/Q".$quarter);
                $availableReports[$i] = array(
                    'caption' => $year."/Q".$quarter,
                    'year' => $year,
                    'quarter' => $quarter,
                    'active' => true
                );
            } else {
                $this->log('not quarter head');
                $availableReports[$i] = array(
                    'active' => false
                );
            }
        }
        
        return $availableReports;
    }

    protected function extractQuarterFromHeader($header)
    {
        $re = '/\d{4}\/Q(\d{1})/';
        $match = preg_match($re, $header, $matches);
        if ($match) {
            return $matches[1];
        }
        return false;
    }

    protected function extractYearFromQuarterHeader($header)
    {
        $re = '/(\d{4})\/Q\d{1}/';
        $match = preg_match($re, $header, $matches);
        
        if ($match) {
            return $matches[1];
        }
        return false;
    }
    
    private function getQuarterlyUrlFromAnnualUrl($annual, $base, $addParameter = true) {
        //my trzymamy np. ACP a oni do strony do raportó kwartalnych mają linka bezpoedniego z ASSECO-POLAND
        // nie mam innego pomysłu na razie niż takie przepięcie -
        // idealnie by było mieć identyfikatory spółek na różnych portalach, ale to też dużo roboty
        $html = file_get_contents($annual);
        $re = '/\/'.$base.'\/[a-zA-Z-_]+,Q/';
        $match = preg_match($re, $html, $matches);
        if($match) {
            $quarterlyUrl = "http://www.biznesradar.pl".$matches[0];
            if($addParameter) {
                $quarterlyUrl .= ",0";
            }
            
            return $quarterlyUrl;
        }
        throw new \Exception('Nieprawidłowy adres do raportu kwartalnego');
    }

    protected function getReportRZISUrl()
    {
        return $this->getQuarterlyUrlFromAnnualUrl(parent::getReportRZISUrl(), 'raporty-finansowe-rachunek-zyskow-i-strat', false);
    }

    protected function getReportBilansUrl()
    {
        return $this->getQuarterlyUrlFromAnnualUrl(parent::getReportBilansUrl(), 'raporty-finansowe-bilans');
    }

    protected function getReportWRUrl()
    {
        return parent::getReportWRUrl();
    }

    public function getReportIdentifier($caption)
    {
        $year = (int)(substr($caption, 0, 4));
        $quarter = (int)(substr($caption, 6, 1));
        $identifierTail = false;
        switch($quarter) {
            case 1:
                $identifierTail = '03-31';
                break;
            case 2:
                $identifierTail = '06-30';
                break;
            case 3:
                $identifierTail = '09-30';
                break;
            case 4:
                $identifierTail = '12-31';
                break;
        }
        if($identifierTail) {
            return $year."-".$identifierTail;
        }
        throw new \Exception("Wrong quarter identifier");
    }
}