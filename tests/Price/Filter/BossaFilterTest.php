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
        $this->sut          = new BossaFilter(new BossaMarketIdTranslator());
        $this->testRawData  = new RawData("ASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nASSECOPOL,20161025,26.55,27.28,26.55,27.10,2431225\nPKOBP,20161025,26.55,27.28,26.55,27.10,2431225\nPGNIG,20161025,26.55,27.28,26.55,27.10,2431225");
    }
}