<?php

namespace Dividend\Parser\Stockwatch;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Report\Parser\InvalidCompanyTypeException;
use Company\Entity\Company;

class StockwatchParser {
    const MIN_YEAR = 2001;
    const CLEAR_OLD_DATA = false;
    var $rows = array ();
    var $parsedData = array ();

    public function parse() {
        if (self::CLEAR_OLD_DATA) {
            $this->deleteDividends();
        }
        $now = (int)date("Y");

        for ($i = MIN_YEAR; $i <= $now; $i++) {
            $this->parseYear($i);
        }
    }

    private function deleteDividends() {
        throw new \Exception('not implementen yet');
    }

    public function parseYear($year) {
        $this->reset();

        $url = $this->getDividendsUrl($year);

        $rawData = $this->getData($url);

        $parsedData = $this->parseYearData($rawData);

        $dividends = $this->getDividendsFromParsedData($parsedData);

        $this->storeDividends($dividends);
    }

    private function reset() {
        $this->rows = array ();
        $this->parsedData = array ();
    }

    private function parseYearData($data) {
        $this->extractRows($data);
    }

    private function extractRows($data) {
        $re = '/<tr[^>]*>(.*?)<\/tr>/s';
        preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            //print_r($match[1]);
            if (count($match) > 1) {
                $this->rows[] = $match[1];
            }
        }
    }

    private function parsePage($url) {
        $this->log('[S] parse page: ' . $url);
        $this->html = $this->getData($url);
        $dom = new Crawler($this->html);

        $table = $this->getHtmlTable($dom);

        $this->availableReports = $this->getAvailableReports($table);

        $reportDataTrs = $this->getReportData($table);

        foreach ($reportDataTrs as $tr) {
            try {
                $this->parseRow($tr);
            } catch(\Exception $e) {
                continue;
            }
        }
    }

    private function parseRow($tr) {
    }

    private function getHtmlTable(Crawler $dom) {
        $header = null;
        $table = $dom->filter('tbody');

        return $table;
    }

    private function getReportDataKey($reportDataName) {
        $translation = array (
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
            'IncomeIntrestIncome' => 'income_part2_bank' );

        if (!array_key_exists($reportDataName, $translation)) {
            throw new \Exception('nieznana dana');
        }

        return $translation[$reportDataName];
    }

    private function getDividendsUrl($year) {
        return "https://www.stockwatch.pl/async/dividendsview.aspx?year=" . $year . "&s=&pp=false";
    }

    public function getData($url) {
        try {
            $html = file_get_contents($url);
        } catch(ContextErrorException $e) {
            throw new Exception('nie udało się pobrać danych');
        }

        return $html;
    }
}