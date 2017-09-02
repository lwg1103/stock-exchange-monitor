<?php
namespace Dividend\Parser\Stockwatch;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Company\Entity\Company;
use Dividend\Entity\Dividend\State;

class StockwatchParser
{

    const CLEAR_OLD_DATA = false;
    
    const DIVIDEND_STATUS_PAID_INDICATOR = 'wypłacona';
    const DIVIDEND_STATUS_PASSED_INDICATOR = 'uchwalona';
    const DIVIDEND_STATUS_PROPOSAL_INDICATOR = 'proponowana';

    var $rows = array();

    var $parsedData = array();
    var $dividends = array();
    
    private function deleteDividends()
    {
        throw new \Exception('not implementen yet');
    }

    public function parseYear($year)
    {
        $this->reset();
        
        $url = $this->getDividendsUrl($year);
        
        $rawData = $this->getData($url);
        
        $parsedData = $this->parseYearData($rawData);
        
        $dividends = $this->getDividendsFromParsedData();
        
        $this->storeDividends($dividends);
    }

    private function reset()
    {
        $this->rows = array();
        $this->parsedData = array();
    }

    private function parseYearData($data)
    {
        $this->extractRows($data);
        
        $this->extractDataFromRows();
    }
    
    private function getDividendsFromParsedData() {
        foreach($this->parsedData as $company => $companyYearDividend) {
            
        }
    }

    private function extractDataFromRows()
    {
        foreach ($this->rows as $row) {
            // wygląda na skomplikowany ale wyciąga po prostu zawartosć każdej komórki
            $re = '/<td[^>]*><strong><a.*>(.*)<\/a>.*<td[^>]*>od&nbsp;(.*)<br\/>do&nbsp;(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>/si';
            preg_match_all($re, $row, $matches, PREG_SET_ORDER, 0);

            foreach ($matches as $match) {
                if (count($match) == 10) {
                    $removeFirstElement = array_shift($match);
                    $match[7] = $this->parseDividendStatus($match[7]);
                    $this->parsedData[$match[0]] = array(
                        'period_from' => $match[1],
                        'period_to' => $match[2],
                        'value' => $match[3],
                        'currency' => 'PLN',
                        'rate' => $match[4],
                        'state' => $match[7],
                        'payment_date' => '',
                        'agm_date' => $match[8],
                        );
                }
            }
            print_r($match);
            print_r($this->parsedData[$match[0]]);
            //die();
        }
    }
    
    private function parseDividendStatus($data) {
        if(strpos($data, self::DIVIDEND_STATUS_PAID_INDICATOR) !== false) {
            return State::PAID;
        }
        if(strpos($data, self::DIVIDEND_STATUS_PASSED_INDICATOR) !== false) {
            return State::PASSED;
        }
        if(strpos($data, self::DIVIDEND_STATUS_PROPOSAL_INDICATOR) !== false) {
            return State::PROPOSAL;
        }
        return '';//$data;
    }

    private function extractRows($data)
    {
        $re = '/<tr[^>]*>(.*?)<\/tr>/s';
        preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            if (count($match) > 1) {
                $this->rows[] = $match[1];
            }
        }
        
        // first row is the header
        $header = array_shift($this->rows);
    }

    private function parsePage($url)
    {
        $this->log('[S] parse page: ' . $url);
        $this->html = $this->getData($url);
        $dom = new Crawler($this->html);
        
        $table = $this->getHtmlTable($dom);
        
        $this->availableReports = $this->getAvailableReports($table);
        
        $reportDataTrs = $this->getReportData($table);
        
        foreach ($reportDataTrs as $tr) {
            try {
                $this->parseRow($tr);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    private function parseRow($tr)
    {}

    private function getHtmlTable(Crawler $dom)
    {
        $header = null;
        $table = $dom->filter('tbody');
        
        return $table;
    }

    private function getReportDataKey($reportDataName)
    {
        $translation = array(
            'IncomeNetProfit' => 'netProfit',
            'BalanceCurrentAssets' => 'currentAssets',
            'BalanceTotalAssets' => 'assets',
            'BalanceCurrentLiabilities' => 'currentLiabilities',
            'BalanceTotalEquityAndLiabilities' => 'liabilities',
            'ShareAmountCurrent' => 'sharesQuantity',
            // 'WKCurrent' => 'bookValue_per_share',
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
        
        if (! array_key_exists($reportDataName, $translation)) {
            throw new \Exception('nieznana dana');
        }
        
        return $translation[$reportDataName];
    }

    private function getDividendsUrl($year)
    {
        return "https://www.stockwatch.pl/async/dividendsview.aspx?year=" . $year . "&s=&pp=false";
    }

    public function getData($url)
    {
        try {
            $html = file_get_contents($url);
        } catch (ContextErrorException $e) {
            throw new Exception('nie udało się pobrać danych');
        }
        
        return $html;
    }
}