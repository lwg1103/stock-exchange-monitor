<?php

namespace ReportParser;

use Company\Entity\Company;
use Company\Entity\Company\Type;
use AppBundle\Utils\ReportParser\Biznesradar\BiznesradarReportParser;
use AppBundle\Utils\ReportParser\InvalidCompanyTypeException;
use Report\Loader\ReportLoader;
use Report\Reader\PArserReportReader;
use Report\Entity\Report;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BiznesradarParserTest extends KernelTestCase
{
    private $sut;
    private $bankCompany;
    private $ordinaryCompany;
    private $em;


    /**
     * @test
     *
     * @expectedException AppBundle\Utils\ReportParser\InvalidCompanyTypeException
     */
    public function throwsExceptionIfWrongCompanyType()
    {
    	$wrongCompany = new Company("Trolo", "TRL", "trtrt type");
        $this->sut->parse($wrongCompany);
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
    	$this->checkIfCompanyHasReport($this->ordinaryCompany);
    }
    
    /**
     * @test
     */
    public function checkIfBankCompanyHasReport() {
    	$this->checkIfCompanyHasReport($this->bankCompany);
    }
    	
    private function checkIfCompanyHasReport($company) {
    	$year = date('Y', strtotime('-1 year'));
    	
    	$storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
    			'company' => $company,
    			
    			'period' => Report\Period::ANNUAL,
    			'type' => Report\Type::AUTO
    	]);
    	
    	$this->assertNotNull($storedReport);
    	
    	$this->assertFalse($this->sut->needStoreReport($storedReport));
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
    	$reportLoader = $prophet->prophesize(ReportLoader::class);
    	
        $this->sut = new BiznesradarReportParser($this->em->getRepository('ReportContext:Report'), new ParserReportReader(), new ReportLoader($this->em));
        
        $this->ordinaryCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'ACP'));
        $this->bankCompany = $this->em->getRepository('CompanyContext:Company')->findOneBy(array('marketId' => 'PKO'));
       
		$this->sut->parse($this->ordinaryCompany);
		$this->sut->parse($this->bankCompany);
    }

}