<?php

namespace Price\Filter;

use Company\Translator\BossaMarketIdTranslator;
use Price\Downloader\RawData;

class BossaFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaFilter */
    private $sut;
    /** @var RawData */
    private $testRawData;
    /** @var EntityRepository|ObjectProphecy */
    private $companyRepository;

    /**
     * @test
     */
    public function returnsFilteredData()
    {
        $this->assertInstanceOf(
            FilteredData::class,
            $this->sut->filter($this->testRawData, "PKO")
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
        $this->sut->filter($this->testRawData, "ELB");
    }

    /**
     * @test
     *
     * @expectedException Price\Filter\FilterException
     * @expectedExceptionMessage One result for ASSECOPOL expected, 2 received
     */
    public function throwsExceptionIfFilteringReturnsTooManyResults()
    {
        $this->sut->filter($this->testRawData, "ACP");
    }

    /**
     * @test
     */
    public function returnsProperString()
    {
        $this->assertEquals(
            new FilteredData("PGNIG,20161025,26.55,27.28,26.55,27.10,2431225"),
            $this->sut->filter($this->testRawData, "PGN")
        );
    }

    protected function setUp()
    {
        $prophet = new Prophet();
        $this->companyRepository = $prophet->prophesize(EntityRepository::class);
        $pgn = new Company('PGNiG', 'PGN', Type::ORDINARY, 'PGNIG');
        $acp = new Company('ASSECOPOL', 'ACP', Type::ORDINARY, 'ASSECOPOL');
        $elb = new Company('ELBUD', 'ELB', Type::ORDINARY, 'ELBUDOWA');
        $pko = new Company('PKOBP', 'PKO', Type::ORDINARY, 'PKOBP');
        $this->companyRepository->findOneBy(['marketId' => 'PGN'])->willReturn($pgn);
        $this->companyRepository->findOneBy(['longMarketId' => 'PGNIG'])->willReturn($pgn);
        $this->companyRepository->findOneBy(['marketId' => 'ACP'])->willReturn($acp);
        $this->companyRepository->findOneBy(['longMarketId' => 'ASSECOPOL'])->willReturn($acp);
        $this->companyRepository->findOneBy(['marketId' => 'ELB'])->willReturn($elb);
        $this->companyRepository->findOneBy(['longMarketId' => 'ELBUDOWA'])->willReturn($elb);
        $this->companyRepository->findOneBy(['marketId' => 'PKO'])->willReturn($pko);
        $this->companyRepository->findOneBy(['longMarketId' => 'PKOBP'])->willReturn($pko);
        
        $this->sut          = new BossaFilter(new BossaMarketIdTranslator($this->companyRepository->reveal()));
        $this->testRawData  = new RawData("ASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nPKOBP,20161025,26.55,27.28,26.55,27.10,2431225\nPGNIG,20161025,26.55,27.28,26.55,27.10,2431225");
    }
}