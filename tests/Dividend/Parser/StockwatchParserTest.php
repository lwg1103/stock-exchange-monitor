<?php

namespace Dividend\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Report\Parser\Biznesradar\BiznesradarParser;
use Report\Parser\Biznesradar\BiznesradarQuarterlyParser;
use Report\Parser\InvalidCompanyTypeException;
use Report\Loader\ReportLoader;
use Report\Reader\ParserReportReader;
use Report\Reader\ParserQuarterlyReportReader;
use Report\Entity\Report;
use Application\UseCase\GetCompanyOnlineAnnualReports;
use Application\UseCase\GetCompanyOnlineQuarterlyReports;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Monolog\Logger;

class StockwatchParserTest extends KernelTestCase
{
    private $sutAnnual;
    private $sutQuarterly;
    private $bankCompany;
    private $ordinaryCompany;
    private $em;
    private $reportLoader;
    private $parserAnnual;
    private $parserQuarterly;


    /**
     * @test
     *
     * @expectedException Report\Parser\InvalidCompanyTypeException
     */
    public function throwsExceptionIfWrongCompanyType()
    {
    	$wrongCompany = new Company("Trolo", "TRL", "trtrt type");
        $this->sutAnnual->parseLoadReport($wrongCompany);
    }

    /**
     * @test
     *
     */
    public function getsHtmlPageContentFromWeb() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
    	$url = "http://www.biznesradar.pl/wskazniki-wartosci-rynkowej/" . $ordinaryCompany->getMarketId();
    	$html = $this->parserAnnual->getData($url);

    	$this->assertThat(
    			$html,
    			$this->stringContains('<body'));
    	$this->assertThat(
    			$html,
    			$this->stringContains('</body>'));
    	$this->assertThat(
    			$html,
    			$this->stringContains('<html'));
    	$this->assertThat(
    			$html,
    			$this->stringContains('</html>'));
    	$this->assertThat(
    			$html,
    			$this->stringContains('class="report-table"'));

    }

    /**
     * @test
     */
    public function checkIfOrdinaryCompanyHasReport() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->sutAnnual->parseLoadReport($ordinaryCompany);
    	$this->checkIfCompanyHasAnnualReport($ordinaryCompany);
    	$this->checkIfCompanyHasQuarterlyReports($ordinaryCompany);
    }

    /**
     * @test
     */
    public function checkIfBankCompanyHasReport() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sutAnnual->parseLoadReport($bankCompany);
    	$this->checkIfCompanyHasAnnualReport($bankCompany);
    	$this->checkIfCompanyHasQuarterlyReports($bankCompany);
    }

    private function checkIfCompanyHasAnnualReport($company) {

    	$year = date('Y', strtotime('-1 year'));

    	$storedReport = $this->getStoredAnnualReport($company, $year);

    	$this->assertNotNull($storedReport);

    	$this->assertFalse($this->reportLoader->needStoreReport($storedReport));
    }
    
    private function checkIfCompanyHasQuarterlyReports($company) {
    
        $year = date('Y', strtotime('-1 year'));
    
        $storedReport = $this->getStoredAnnualReport($company, $year);
    
        $this->assertNotNull($storedReport);
    
        $this->assertFalse($this->reportLoader->needStoreReport($storedReport));
    }

    /**
     * @test
     */
    public function checkAnnualReportForOrdinaryCompany() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->sutAnnual->parseLoadReport($ordinaryCompany);
        $report = $this->getStoredAnnualReport($ordinaryCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 7932000);
        $this->assertEquals($report->getNetProfit(), 301300);
        $this->assertEquals($report->getOperationalNetProfit(), 769400);
        $this->assertEquals($report->getBookValue(), 8670600);
        $this->assertEquals($report->getAssets(), 12791200);
        $this->assertEquals($report->getLiabilities(), 12791200);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getCurrentAssets(), 4331800);
        $this->assertEquals($report->getCurrentLiabilities(), 2495900);
        $this->assertEquals($report->getSharesQuantity(), 83000303);
    }

    /**
     * @test
     */
    public function checkAnnualReportForBankCompany() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sutAnnual->parseLoadReport($bankCompany);
        $report = $this->getStoredAnnualReport($bankCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 13544000);
        $this->assertEquals($report->getNetProfit(), 2874000);
        $this->assertEquals($report->getOperationalNetProfit(), 648500);
        $this->assertEquals($report->getBookValue(), 32568600);
        $this->assertEquals($report->getAssets(), 285572700);
        $this->assertEquals($report->getLiabilities(), 285572700);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getSharesQuantity(), 1250000000);
    }

    /**
     * @test
     */
    public function checkAnnualReportWithNegativeValues() {
        $negativeCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'KGH'));
        $this->sutAnnual->parseLoadReport($negativeCompany);
        $report = $this->getStoredAnnualReport($negativeCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 19156000);
        $this->assertEquals($report->getNetProfit(), -4371000);
        $this->assertEquals($report->getOperationalNetProfit(), -3219000);
        $this->assertEquals($report->getBookValue(), 15911000);
        $this->assertEquals($report->getAssets(), 33442000);
        $this->assertEquals($report->getLiabilities(), 33442000);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getCurrentAssets(), 6240000);
        $this->assertEquals($report->getCurrentLiabilities(), 5866000);
        $this->assertEquals($report->getSharesQuantity(), 200000000);
    }
    
    /**
     * @test
     */
    public function checkQuarterlyReportForOrdinaryCompany() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->sutQuarterly->parseLoadReport($ordinaryCompany);
        $report = $this->getStoredQuarterlyReport($ordinaryCompany, 2016, 2);
    
        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 1926200);
        $this->assertEquals($report->getNetProfit(), 77300);
        $this->assertEquals($report->getOperationalNetProfit(), 176400);
        $this->assertEquals($report->getBookValue(), 8366500);
        $this->assertEquals($report->getAssets(), 11921200);
        $this->assertEquals($report->getLiabilities(), 11921200);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getCurrentAssets(), 3953200);
        $this->assertEquals($report->getCurrentLiabilities(), 2341200);
        $this->assertEquals($report->getSharesQuantity(), 83000303);
    }
    
    /**
     * @test
     */
    public function checkQuarterlyReportForBankCompany() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sutQuarterly->parseLoadReport($bankCompany);
        $report = $this->getStoredQuarterlyReport($bankCompany, 2015, 4);
    
        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 3324770);
        $this->assertEquals($report->getNetProfit(), 444257);
        $this->assertEquals($report->getOperationalNetProfit(), 176893);
        $this->assertEquals($report->getBookValue(), 30264913);
        $this->assertEquals($report->getAssets(), 266939919);
        $this->assertEquals($report->getLiabilities(), 266939919);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getSharesQuantity(), 1250000000);
    }
    
    /**
     * @test
     */
    public function checkQurterlyReportWithNegativeValues() {
        $negativeCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'KGH'));
        $this->sutQuarterly->parseLoadReport($negativeCompany);
        $report = $this->getStoredQuarterlyReport($negativeCompany, 2016, 4);
    
        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 6015000);
        $this->assertEquals($report->getNetProfit(), -4996000);
        $this->assertEquals($report->getOperationalNetProfit(), -4400000);
        $this->assertEquals($report->getBookValue(), 15911000);
        $this->assertEquals($report->getAssets(), 33442000);
        $this->assertEquals($report->getLiabilities(), 33442000);
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals($report->getCurrentAssets(), 6240000);
        $this->assertEquals($report->getCurrentLiabilities(), 5866000);
        $this->assertEquals($report->getSharesQuantity(), 200000000);
    }

    private function getStoredAnnualReport($company, $year) {
        $storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
            'company' => $company,
            'identifier' => Carbon::createFromFormat("Y-m-d", $this->parserAnnual->getReportIdentifier($year), 'Europe/Warsaw'),
            'period' => Report\Period::ANNUAL,
            'type' => Report\Type::AUTO
        ]);

        return $storedReport;
    }
    
    private function getStoredQuarterlyReport($company, $year, $quarter) {
        $storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
            'company' => $company,
            'identifier' => Report\Period::getIdentifierForYearAndQuarter($year, $quarter),
            'period' => Report\Period::QUARTERLY,
            'type' => Report\Type::AUTO
        ]);
    
        return $storedReport;
    }

    public function thereAreOurCompaniesTypesOnly() {
    	$companies = $this->em->getRepository('CompanyContext:Company')->findAll();
    	$types = $this->sutAnnual->getAvailableCompanyTypes();
    	foreach($companies as $company) {
    		$this->assertTrue(in_array($company->getType(), $types));
    	}
    }

    protected function setUp()
    {
    	self::bootKernel();

    	$this->em = static::$kernel->getContainer()
				    	->get('doctrine')
				    	->getManager();

    	$prophet = new Prophet();
        $this->reportLoader = static::$kernel->getContainer()->get('app.loader.parser_report_loader');
    	$logger = $prophet->prophesize(Logger::class);

        $this->parserAnnual = new BiznesradarParser($this->em->getRepository('ReportContext:Report'), new ParserReportReader(), $logger->reveal());
        $this->parserQuarterly = new BiznesradarQuarterlyParser($this->em->getRepository('ReportContext:Report'), new ParserQuarterlyReportReader(), $logger->reveal());
        $this->sutAnnual = new GetCompanyOnlineAnnualReports($this->parserAnnual, $this->reportLoader);
        $this->sutQuarterly = new GetCompanyOnlineAnnualReports($this->parserQuarterly, $this->reportLoader);
    }

}