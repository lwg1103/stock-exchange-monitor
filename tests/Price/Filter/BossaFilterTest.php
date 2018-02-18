<?php

namespace Price\Filter;

use Price\Downloader\RawData;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Doctrine\ORM\EntityRepository;
use Prophecy\Prophet;

class BossaFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaFilter */
    private $sut;
    /** @var RawData */
    private $testRawData;
    /** @var EntityRepository|ObjectProphecy */
    private $companyRepository;
    
    /** @var Company */
    private $companyPko;
    private $companyPgn;
    private $companyAcp;
    private $companyElb;

    /**
     * @test
     */
    public function returnsFilteredData()
    {
        $this->assertInstanceOf(
            FilteredData::class,
            $this->sut->filter($this->testRawData, $this->companyPko)
        );
    }

    /**
     * @test
     *
     * @expectedException Price\Filter\FilterException
     * @expectedExceptionMessage One result for ELBUDOWA expected, 0 received
     */
    public function throwsExceptionIfFilteringReturnsNoResult()
    {
        $this->sut->filter($this->testRawData, $this->companyElb);
    }

    /**
     * @test
     *
     * @expectedException Price\Filter\FilterException
     * @expectedExceptionMessage One result for ASSECOPOL expected, 2 received
     */
    public function throwsExceptionIfFilteringReturnsTooManyResults()
    {
        $this->sut->filter($this->testRawData, $this->companyAcp);
    }

    /**
     * @test
     */
    public function returnsProperString()
    {
        $this->assertEquals(
            new FilteredData("PGNIG,20161025,26.55,27.28,26.55,27.10,2431225"),
            $this->sut->filter($this->testRawData, $this->companyPgn)
        );
    }

    protected function setUp()
    {
        $prophet = new Prophet();
        $this->companyRepository = $prophet->prophesize(EntityRepository::class);
        $this->companyPgn = new Company('PGNiG', 'PGN', Type::ORDINARY, 'PGNIG');
        $this->companyAcp = new Company('ASSECOPOL', 'ACP', Type::ORDINARY, 'ASSECOPOL');
        $this->companyElb = new Company('ELBUD', 'ELB', Type::ORDINARY, 'ELBUDOWA');
        $this->companyPko = new Company('PKOBP', 'PKO', Type::ORDINARY, 'PKOBP');
        $this->companyRepository->findOneBy(['marketId' => 'PGN'])->willReturn($this->companyPgn);
        $this->companyRepository->findOneBy(['longMarketId' => 'PGNIG'])->willReturn($this->companyPgn);
        $this->companyRepository->findOneBy(['marketId' => 'ACP'])->willReturn($this->companyAcp);
        $this->companyRepository->findOneBy(['longMarketId' => 'ASSECOPOL'])->willReturn($this->companyAcp);
        $this->companyRepository->findOneBy(['marketId' => 'ELB'])->willReturn($this->companyElb);
        $this->companyRepository->findOneBy(['longMarketId' => 'ELBUDOWA'])->willReturn($this->companyElb);
        $this->companyRepository->findOneBy(['marketId' => 'PKO'])->willReturn($this->companyPko);
        $this->companyRepository->findOneBy(['longMarketId' => 'PKOBP'])->willReturn($this->companyPko);
        
        $this->sut          = new BossaFilter();
        $this->testRawData  = new RawData("ASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nPKOBP,20161025,26.55,27.28,26.55,27.10,2431225\nPGNIG,20161025,26.55,27.28,26.55,27.10,2431225");
    }
}