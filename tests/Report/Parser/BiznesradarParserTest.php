<?php

namespace Report\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Report\Parser\Biznesradar\BiznesradarParser;
use Report\Parser\InvalidCompanyTypeException;
use Report\Loader\ReportLoader;
use Report\Reader\ParserReportReader;
use Report\Entity\Report;
use Application\UseCase\GetCompanyOnlineAnnualReports;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Monolog\Logger;

class BiznesradarParserTest extends KernelTestCase
{
    private $sut;
    private $bankCompany;
    private $ordinaryCompany;
    private $em;
    private $reportLoader;
    private $parser;


    /**
     * @test
     *
     * @expectedException Report\Parser\InvalidCompanyTypeException
     */
    public function throwsExceptionIfWrongCompanyType()
    {
    	$wrongCompany = new Company("Trolo", "TRL", "trtrt type");
        $this->sut->parseLoadReport($wrongCompany);
    }

    /**
     * @test
     *
     */
    public function getsHtmlPageContentFromWeb() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
    	$url = "http://www.biznesradar.pl/wskazniki-wartosci-rynkowej/" . $ordinaryCompany->getMarketId();
    	$html = $this->parser->getData($url);

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
        $this->sut->parseLoadReport($ordinaryCompany);
    	$this->checkIfCompanyHasReport($ordinaryCompany);
    }

    /**
     * @test
     */
    public function checkIfBankCompanyHasReport() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sut->parseLoadReport($bankCompany);
    	$this->checkIfCompanyHasReport($bankCompany);
    }

    private function checkIfCompanyHasReport($company) {

    	$year = date('Y', strtotime('-1 year'));

    	$storedReport = $this->getStoredReport($company, $year);

    	$this->assertNotNull($storedReport);

    	$this->assertFalse($this->reportLoader->needStoreReport($storedReport));
    }

    /**
     * @test
     */
    public function checkReportForOrdinaryCompany() {
        $ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->sut->parseLoadReport($ordinaryCompany);
        $report = $this->getStoredReport($ordinaryCompany, 2016);

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
    public function checkReportForBankCompany() {
        $bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
        $this->sut->parseLoadReport($bankCompany);
        $report = $this->getStoredReport($bankCompany, 2016);

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
    public function checkReportWithNegativeValues() {
        $negativeCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'KGH'));
        $this->sut->parseLoadReport($negativeCompany);
        $report = $this->getStoredReport($negativeCompany, 2016);

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

    private function getStoredReport($company, $year) {
        $storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
            'company' => $company,
            'identifier' => Carbon::createFromFormat("Y-m-d", $this->parser->getReportIdentifier($year), 'Europe/Warsaw'),
            'period' => Report\Period::ANNUAL,
            'type' => Report\Type::AUTO
        ]);

        return $storedReport;
    }

    public function thereAreOurCompaniesTypesOnly() {
    	$companies = $this->em->getRepository('CompanyContext:Company')->findAll();
    	$types = $this->sut->getAvailableCompanyTypes();
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

        $this->parser = new BiznesradarParser($this->em->getRepository('ReportContext:Report'), new ParserReportReader(), $logger->reveal());
        $this->sut = new GetCompanyOnlineAnnualReports($this->parser, $this->reportLoader);
    }

}