<?php

namespace Report\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Report\Parser\Biznesradar\BiznesradarParser;
use Report\Parser\Biznesradar\BiznesradarQuarterlyParser;
use Report\Reader\ParserReportReader;
use Report\Reader\ParserQuarterlyReportReader;
use Report\Entity\Report;
use Application\UseCase\GetCompanyOnlineAnnualReports;
use Application\UseCase\GetCompanyOnlineQuarterlyReports;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Monolog\Logger;

class BiznesradarParserTest extends KernelTestCase
{
    private $sutAnnual;
    private $sutQuarterly;
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

    	$year = date('Y', strtotime('-2 year'));

    	$storedReport = $this->getStoredAnnualReport($company, $year);

    	$this->assertNotNull($storedReport);

    	$this->assertFalse($this->reportLoader->needStoreReport($storedReport));
    }
    
    private function checkIfCompanyHasQuarterlyReports($company) {
    
        $year = date('Y', strtotime('-2 year'));
    
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
        $this->assertEquals(7932000, $report->getIncome());
        $this->assertEquals(301300, $report->getNetProfit());
        $this->assertEquals(769400, $report->getOperationalNetProfit());
        $this->assertEquals(8670600, $report->getBookValue());
        $this->assertEquals(12791200, $report->getAssets());
        $this->assertEquals(12791200, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(4331800, $report->getCurrentAssets());
        $this->assertEquals(2495900, $report->getCurrentLiabilities());
        $this->assertEquals(83000303, $report->getSharesQuantity());
    }

    /**
     * @test
     */
    public function checkAnnualReportForBankCompany() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sutAnnual->parseLoadReport($bankCompany);
        $report = $this->getStoredAnnualReport($bankCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals(13544000, $report->getIncome());
        $this->assertEquals(2874000, $report->getNetProfit());
        $this->assertEquals(648500, $report->getOperationalNetProfit());
        $this->assertEquals(32568600, $report->getBookValue());
        $this->assertEquals(285572700, $report->getAssets());
        $this->assertEquals(285572700, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(1250000000, $report->getSharesQuantity());
    }

    /**
     * @test
     */
    public function checkAnnualReportWithNegativeValues() {
        $negativeCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'KGH'));
        $this->sutAnnual->parseLoadReport($negativeCompany);
        $report = $this->getStoredAnnualReport($negativeCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals(19156000, $report->getIncome());
        $this->assertEquals(-4371000, $report->getNetProfit());
        $this->assertEquals(-3219000, $report->getOperationalNetProfit());
        $this->assertEquals(15911000, $report->getBookValue());
        $this->assertEquals(33442000, $report->getAssets());
        $this->assertEquals(33442000, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(6240000, $report->getCurrentAssets());
        $this->assertEquals(5866000, $report->getCurrentLiabilities());
        $this->assertEquals(200000000, $report->getSharesQuantity());
    }
    
    /**
     * @test
     */
    public function checkQuarterlyReportForOrdinaryCompany() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->sutQuarterly->parseLoadReport($ordinaryCompany);
        $report = $this->getStoredQuarterlyReport($ordinaryCompany, 2016, 2);
    
        $this->assertNotNull($report);
        $this->assertEquals(1926200, $report->getIncome());
        $this->assertEquals(77300, $report->getNetProfit());
        $this->assertEquals(176400, $report->getOperationalNetProfit());
        $this->assertEquals(8366500, $report->getBookValue());
        $this->assertEquals(11921200, $report->getAssets());
        $this->assertEquals(11921200, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(3953200, $report->getCurrentAssets());
        $this->assertEquals(2341200, $report->getCurrentLiabilities());
        $this->assertEquals(83000303, $report->getSharesQuantity());
    }
    
    /**
     * @test
     */
    public function checkQuarterlyReportForBankCompany() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sutQuarterly->parseLoadReport($bankCompany);
        $report = $this->getStoredQuarterlyReport($bankCompany, 2015, 4);
    
        $this->assertNotNull($report);
        $this->assertEquals(3324770, $report->getIncome());
        $this->assertEquals(444257, $report->getNetProfit());
        $this->assertEquals(176893, $report->getOperationalNetProfit());
        $this->assertEquals(30264913, $report->getBookValue());
        $this->assertEquals(266939919, $report->getAssets());
        $this->assertEquals(266939919, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(1250000000, $report->getSharesQuantity());
    }
    
    /**
     * @test
     */
    public function checkQurterlyReportWithNegativeValues() {
        $negativeCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'KGH'));
        $this->sutQuarterly->parseLoadReport($negativeCompany);
        $report = $this->getStoredQuarterlyReport($negativeCompany, 2016, 4);
    
        $this->assertNotNull($report);
        $this->assertEquals(6015000, $report->getIncome());
        $this->assertEquals(-4996000, $report->getNetProfit());
        $this->assertEquals(-4400000, $report->getOperationalNetProfit());
        $this->assertEquals(15911000, $report->getBookValue());
        $this->assertEquals(33442000, $report->getAssets());
        $this->assertEquals(33442000, $report->getLiabilities());
        //pasywa = aktywa - inaczej cos jest źle z raportem
        $this->assertEquals($report->getLiabilities(), $report->getAssets());
        $this->assertEquals(6240000, $report->getCurrentAssets());
        $this->assertEquals(5866000, $report->getCurrentLiabilities());
        $this->assertEquals(200000000, $report->getSharesQuantity());
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
        $this->sutQuarterly = new GetCompanyOnlineQuarterlyReports($this->parserQuarterly, $this->reportLoader);
    }

}