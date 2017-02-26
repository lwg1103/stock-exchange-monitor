<?php

namespace Parser;

use Company\Entity\Company;
use Company\Entity\Company\Type;
use AppBundle\Utils\ReportParser\Biznesradar\BiznesradarReportParser;
use AppBundle\Utils\ReportParser\InvalidCompanyTypeException;
use Doctrine\ORM\EntityRepository;
use Report\Loader\ReportLoader;
use Report\Reader\ReportReader;
use Prophecy\Prophet;

class BiznesradarParserTest extends \PHPUnit_Framework_TestCase
{
    private $sut;
    private $bankCompany;
    private $ordinaryCompany;


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

    protected function setUp()
    {
    	$prophet = new Prophet();
    	$entityRepository = $prophet->prophesize(EntityRepository::class);
    	//$entityRepository->findOneBy(['marketId' => 'PGN'])->willReturn(new Company('PGNiG', 'PGN', Type::ORDINARY));
    	$reportLoader = $prophet->prophesize(ReportLoader::class);
    	$parserReportReader = $prophet->prophesize(ReportReader::class);
    	
        $this->sut = new BiznesradarReportParser($entityRepository->reveal(), $parserReportReader->reveal(), $reportLoader->reveal());
        
        $this->ordinaryCompany = new Company("Asseco Poland", "ACP", Type::ORDINARY);
        $this->bankCompany = new Company("PKO BP", "PKO", Type::BANK);
    }

}