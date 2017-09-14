<?php
namespace Dividend\Parser\Stockwatch;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Company\Entity\Company;
use Dividend\Entity\Dividend\State;
use Dividend\Reader\ParserDividendReader;
use Application\UseCase\ListCompanies;

class StockwatchParser
{
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
            $rowParser = new StockwatchRowParser();
            $parsedRow = $rowParser->extractDataFromRow($row);
            try {
                $parsedRow['company'] = $this->getCompanyForLongMarketId($parsedRow['company']);
            } catch(\Exception $e) {
                continue;
            }
            $this->parsedData[] = $parsedRow;
        }
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

        // first row is the header - delete it
        array_shift($this->rows);
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
            throw new Exception('can not download data');
        }
        
        return $html;
    }
}