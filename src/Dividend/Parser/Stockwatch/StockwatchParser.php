<?php
namespace Dividend\Parser\Stockwatch;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Company\Entity\Company;
use Dividend\Entity\Dividend\State;
use Dividend\Reader\ParserDividendReader;
use Application\UseCase\ListCompanies;

class StockwatchParser
{

    const CLEAR_OLD_DATA = false;
    
    const DIVIDEND_STATUS_PAID_INDICATOR = 'wypłacona';
    const DIVIDEND_STATUS_PASSED_INDICATOR = 'uchwalona';
    const DIVIDEND_STATUS_PROPOSAL_INDICATOR = 'proponowana';

    var $rows = array();

    var $parsedData = array();
    var $dividends = array();
    
    /** @var EntityRepository */
    private $listCompanies;
    private $reader;
    private $companies;
    
    
    /**
     * StockwatchParser constructor.
     * @param ListCompanies $listCompanies use case
     */
    public function __construct(ListCompanies $listCompanies, ParserDividendReader $reader)
    {
        $this->listCompanies = $listCompanies;
        $this->reader = $reader;
        $this->buildCompaniesList();
    }

    public function parseYear($year)
    {
        $this->reset();
        
        $url = $this->getDividendsUrl($year);
        
        $rawData = $this->getData($url);

        $this->parseYearData($rawData);

        return $this->getParsedDividends();
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
        
        return $this->parsedData;
    }

    private function extractDataFromRows()
    {
        
        foreach ($this->rows as $row) {
            // wygląda na skomplikowany ale wyciąga po prostu zawartosć każdej komórki
            $re = '/<td[^>]*><strong><a.*>(.*)<\/a>.*<td[^>]*>od&nbsp;(.*)<br\/>do&nbsp;(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>/si';
            preg_match_all($re, $row, $matches, PREG_SET_ORDER, 0);

            if($match = $matches[0]) {
                if (count($match) == 10) {
                    $removeFirstElement = array_shift($match);
                    
                    $match[7] = trim($match[7]);
                    $re = '/(<span[^>]*>!<\/span>)?(<a[^>]*>)?([^<]*)(<\/a>)?[^>]*<div class="stcm">([0-9-]+)?<\/div>/is';
                    
                    preg_match($re, $match[7], $status, PREG_OFFSET_CAPTURE, 0);
                    if(!count($status) || count($status) < 3) {
                        echo 'skipping';
                        var_dump($match[7]);
                        var_dump($status);die;
                        continue;
                    }
                    $match[7] = trim($status[3][0]);
                    $match[4] = str_replace(array('%', ','), array('', '.'), $match[4]);
                    $match[3] = str_replace(array('%', ','), array('', '.'), $match[3]);
                    $payment_date = '';
                    if(count($status) >= 6) {
                        $payment_date = $status[5][0];
                    }
                    $match[7] = $this->parseDividendStatus($match[7]);
                    try {
                    $this->parsedData[] = array(
                        'company' => $this->getCompanyForLongMarketId($match[0]),
                        'period_from' => $match[1],
                        'period_to' => $match[2],
                        'value' => ((float)$match[3]*100),//cena będzie przetrzymywana w groszach
                        'currency' => 'PLN',
                        'rate' => (float)$match[4],
                        'state' => $match[7],
                        'payment_date' => $payment_date,
                        'agm_date' => $match[8],
                        );
                    
                    } catch (\Exception $e) {
                        //do notfing, just skip this dividend
                        //echo 'there is no marketId: '.$match[0];
                    }
                }
            }
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

    private function getHtmlTable(Crawler $dom)
    {
        $header = null;
        $table = $dom->filter('tbody');
        
        return $table;
    }

    private function getDividendsUrl($year)
    {
        return "https://www.stockwatch.pl/async/dividendsview.aspx?year=" . $year . "&s=&pp=false";
    }
    
    /**
     * @param $longMarketId
     *
     * @return Company
     */
    private function buildCompaniesList()
    {
        $companies = $this->listCompanies->execute();
        
        foreach($companies as $company) {
            $this->companies[$company->getLongMarketId()] = $company;
        }
    }
    
    private function getCompanyForLongMarketId($longMarketId) {
        if(array_key_exists($longMarketId, $this->companies)) {
            return $this->companies[$longMarketId];
        }
        
        throw new \Exception('Wrong longMarketId: '.$longMarketId);
    }
    
    private function getParsedDividends() {
        $parsedDividends = array();
        foreach ($this->parsedData as $dividend) {
            $parsedDividends[] = $this->reader->read($dividend);
        }
        
        return $parsedDividends;
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