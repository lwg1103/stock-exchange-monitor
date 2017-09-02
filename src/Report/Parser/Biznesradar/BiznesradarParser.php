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

class BiznesradarParser extends Parser implements ParserInterface
{

    var $reports = array();

    var $availableReports = array();

    const REPORT_HEADER_QUARTER_INDICATOR = "/Q";

    const REPORT_HEADER_FOURTH_QUARTER = "/Q4";

    public function parse(Company $company)
    {
        $this->reset();
        
        $this->log('[S] parse company: ' . $company->getMarketId());
        $this->company = $company;
        
        $this->checkCompany($company);
        
        $urls = array();
        $urls[] = $this->getReportWRUrl();
        $urls[] = $this->getReportBilansUrl();
        $urls[] = $this->getReportRZISUrl();
        
        foreach ($urls as $url) {
            $this->parsePage($url);
        }
        
        $captions = array_keys($this->reports);
        print_r($captions);echo 'cnt:'.count($this->reports)."\n";
        
        // add company info to parsed reports
        // prepare income value from income parts
        foreach ($captions as $caption) {
            echo $caption."\n";
            
            //there can be header but no required data
            if (!$this->checkMinimalData($caption)) {
                unset($this->reports[$caption]);
                echo 'rcnt:'.count($this->reports);
                continue;
            }
            echo 'after min ';
            $this->reports[$caption]['identifier'] = Carbon::createFromFormat("Y-m-d", $this->getReportIdentifier($caption), 'Europe/Warsaw');
            $this->reports[$caption]['company'] = $this->company;
            if (isset($this->reports[$caption]['income_part1'])) {
                $this->reports[$caption]['income'] = $this->reports[$caption]['income_part1'];
            }
            
            if ($this->company->getType() == Type::BANK) {
                $this->reports[$caption]['income'] = $this->reports[$caption]['income_part1_bank'] + $this->reports[$caption]['income_part2_bank'];
                $this->reports[$caption]['operationalNetProfit'] = $this->reports[$caption]['operationalNetProfit_bank'];
                $this->reports[$caption]['bookValue'] = $this->reports[$caption]['bookValue_bank'];
                $this->reports[$caption]['currentAssets'] = 0; // $this->reports[$caption]['bookValue_bank'];
                $this->reports[$caption]['currentLiabilities'] = 0; // $this->reports[$year]['bookValue_bank'];
            }
        }
        echo 'parsed reports: '.count($this->reports);
        return $this->getParsedReports();
    }

    protected function checkMinimalData($caption)
    {
        if (!isset($this->reports[$caption]['sharesQuantity']) ||is_array($this->reports[$caption]['sharesQuantity']) || ! strlen($this->reports[$caption]['sharesQuantity'])) {
            return false;
        }
        
        if (!isset($this->reports[$caption]['liabilities']) || is_array($this->reports[$caption]['liabilities']) || ! strlen($this->reports[$caption]['liabilities'])) {
            return false;
        }
        
        if (!isset($this->reports[$caption]['assets']) || is_array($this->reports[$caption]['assets']) || ! strlen($this->reports[$caption]['assets'])) {
            return false;
        }
        
        
        
        if ($this->company->getType() == Type::BANK) {
            if (!isset($this->reports[$caption]['income_part1_bank']) || is_array($this->reports[$caption]['income_part1_bank']) || ! strlen($this->reports[$caption]['income_part1_bank'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['bookValue_bank']) || is_array($this->reports[$caption]['bookValue_bank']) || ! strlen($this->reports[$caption]['bookValue_bank'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['operationalNetProfit_bank']) || is_array($this->reports[$caption]['operationalNetProfit_bank']) || ! strlen($this->reports[$caption]['operationalNetProfit_bank'])) {
                return false;
            }
        } elseif ($this->company->getType() == Type::ORDINARY) {
            if (!isset($this->reports[$caption]['income_part1']) || is_array($this->reports[$caption]['income_part1']) || ! strlen($this->reports[$caption]['income_part1'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['currentAssets']) || is_array($this->reports[$caption]['currentAssets']) || ! strlen($this->reports[$caption]['currentAssets'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['currentLiabilities']) || is_array($this->reports[$caption]['currentLiabilities']) || ! strlen($this->reports[$caption]['currentLiabilities'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['bookValue']) || is_array($this->reports[$caption]['bookValue']) || ! strlen($this->reports[$caption]['bookValue'])) {
                return false;
            }
            
            if (!isset($this->reports[$caption]['operationalNetProfit']) || is_array($this->reports[$caption]['operationalNetProfit']) || ! strlen($this->reports[$caption]['operationalNetProfit'])) {
                return false;
            }
        }
        
        if (!isset($this->reports[$caption]['netProfit']) || is_array($this->reports[$caption]['netProfit']) || ! strlen($this->reports[$caption]['netProfit'])) {
            return false;
        }
        
        return true;
    }

    protected function getParsedReports()
    {
        $parsedReports = array();
        foreach ($this->reports as $report) {
            $parsedReports[] = $this->reader->read($report);
        }
        
        return $parsedReports;
    }

    protected function reset()
    {
        $this->reports = array();
        $this->availableReports = array();
    }

    protected function parsePage($url)
    {
        $this->log('[S] parse page: ' . $url);
echo $url."\n";
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

    protected function parseRow($tr)
    {
        $tds = $tr->filter('td[class="h"]')->each(function (Crawler $node, $i) {
            return $node;
        });
        
        $tdsNewest = $tr->filter('td[class="h newest"]')->each(function (Crawler $node, $i) {
            return $node;
        });
        
        if (count($tdsNewest)) {
            $tds[] = $tdsNewest[count($tdsNewest) - 1];
        }
        
        $reportDataKey = $this->getReportDataKey($tr->attr("data-field"));

        for ($i = 0; $i < count($tds); $i ++) {
            if (count($this->availableReports) <= $i) {
                continue;
            }
            
            $reportDataValue = $tds[$i]->filter('span[class="value"] > span[class="pv"]')->each(function (Crawler $node, $i) {
                $value = $node->text();
                return $value;
            });
            
            if (count($reportDataValue)) {
                $reportDataValue = preg_replace('/\ /', '', $reportDataValue[0]);
                $reportDataValue = preg_match_all('/[-+]?[0-9]+/', $reportDataValue, $matches, PREG_SET_ORDER, 0);
                
                if (count($matches) && count($matches[0])) {
                    $reportDataValue = $matches[0][0];
                } else {
                    $reportDataValue = 0;
                }
            }
            
            if ($this->availableReports[$i]['active']) {
                $this->reports[$this->availableReports[$i]['caption']][$reportDataKey] = $reportDataValue;
            }
        }
        
    }

    protected function getHtmlTable(Crawler $dom)
    {
        $header = null;
        $table = $dom->filter('table[class="report-table"]');
        
        return $table;
    }

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
            $year = false;
            if (strpos($reportsHeads[$i], self::REPORT_HEADER_QUARTER_INDICATOR) !== false) {
                $year = $this->extractYearFromQuarterHeader($reportsHeads[$i]);
            } else {
                $year = $this->extractYearFromHeader($reportsHeads[$i]);
            }
            
            if ($year) {
                $this->log('yearly head: ' . $year);
                $availableReports[$i] = array(
                    'caption' => $year,
                    'active' => true
                );
            } else {
                $this->log('not yearly head');
                $availableReports[$i] = array(
                    'active' => false
                );
            }
        }
        
        return $availableReports;
    }

    protected function extractYearFromHeader($header)
    {
        $re = '/\d{4}/';
        $match = preg_match($re, $header, $matches);
        if ($match) {
            return $matches[0];
        }
        return false;
    }

    protected function extractYearFromQuarterHeader($header)
    {
        $header = preg_replace('/\s+/', '', $header);
        if (strpos($header, self::REPORT_HEADER_FOURTH_QUARTER) === false) {
            return false;
        }
        $header = str_replace(self::REPORT_HEADER_FOURTH_QUARTER, '', $header);
        return $this->extractYearFromHeader($header);
    }

    protected function getReportData($table)
    {
        $reportData = $table->filter('tr')->each(function (Crawler $node, $i) {
            return $node;
        });
        
        return $reportData;
    }

    protected function getReportDataKey($reportDataName)
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

    protected function getReportRZISUrl()
    {
        return "http://www.biznesradar.pl/raporty-finansowe-rachunek-zyskow-i-strat/" . $this->company->getMarketId();
    }

    protected function getReportBilansUrl()
    {
        return "http://www.biznesradar.pl/raporty-finansowe-bilans/" . $this->company->getMarketId();
    }

    protected function getReportWRUrl()
    {
        return "http://www.biznesradar.pl/wskazniki-wartosci-rynkowej/" . $this->company->getMarketId();
    }

    public function getData($url)
    {
        try {
            $html = file_get_contents($url);
        } catch (ContextErrorException $e) {
            throw new \Exception('nie udało się pobrać danych');
        }
        
        return $html;
    }

    protected function checkCompany()
    {
        if (! in_array($this->company->getType(), $this->getAvailableCompanyTypes())) {
            throw new InvalidCompanyTypeException();
        }
    }

    public function getAvailableCompanyTypes()
    {
        return array(
            Type::ORDINARY,
            Type::BANK
        );
    }

    public function getReportIdentifier($year)
    {
        return $year . "-12-31";
    }
}