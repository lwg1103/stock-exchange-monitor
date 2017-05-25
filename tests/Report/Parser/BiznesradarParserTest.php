<?php

namespace Report\Parser;

use Company\Entity\Company;
use Company\Entity\Company\Type;
use Report\Parser\Biznesradar\BiznesradarParser;
use Report\Parser\InvalidCompanyTypeException;
use Report\Loader\ReportLoader;
use Report\Reader\ParserReportReader;
use Report\Entity\Report;
use Application\UseCase\GetCompanyOnlineReports;
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
    	$url = "http://www.biznesradar.pl/wskazniki-wartosci-rynkowej/" . $this->ordinaryCompany->getMarketId();
    	$html = $this->sut->getData($url);

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
        $this->sut->parseLoadReport($this->ordinaryCompany);
    	$this->checkIfCompanyHasReport($this->ordinaryCompany);
    }

    /**
     * @test
     */
    public function checkIfBankCompanyHasReport() {
        $this->sut->parseLoadReport($this->bankCompany);
    	$this->checkIfCompanyHasReport($this->bankCompany);
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
        $this->sut->parseLoadReport($this->ordinaryCompany);
        $report = $this->getStoredReport($this->ordinaryCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 7932000);
        $this->assertEquals($report->getNetProfit(), 543600);
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
        $this->sut->parseLoadReport($this->bankCompany);
        $report = $this->getStoredReport($this->bankCompany, 2016);

        $this->assertNotNull($report);
        $this->assertEquals($report->getIncome(), 13544000);
        $this->assertEquals($report->getNetProfit(), 2876100);
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
        $this->assertEquals($report->getNetProfit(), -4449000);
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
            'identifier' => new \DateTime($this->sut->getReportIdentifier($year)),
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

        $parser = new BiznesradarParser($this->em->getRepository('ReportContext:Report'), new ParserReportReader(), $logger->reveal());
        $this->sut = new GetCompanyOnlineReports($parser, $this->reportLoader);
        $this->ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
    }

}