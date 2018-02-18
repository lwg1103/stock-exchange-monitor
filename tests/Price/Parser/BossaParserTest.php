<?php

namespace Price\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Doctrine\ORM\EntityRepository;
use Price\Entity\Price;
use Price\Filter\FilteredData;
use Prophecy\Prophet;

class BossaParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var BossaParser */
    private $sut;
    /** @var FilteredData */
    private $filteredData;
    /** @var EntityRepository|ObjectProphecy */
    private $companyRepository;

    /**
     * @test
     */
    public function returnsPrice()
    {
        $this->assertInstanceOf(
            Price::class,
            $this->sut->parse($this->filteredData)
        );
    }

    /**
     * @test
     */
    public function returnsPriceForProperCompany()
    {
        $this->assertEquals(
            "PGN",
            $this->sut->parse($this->filteredData)->getCompany()->getMarketId()
        );
    }

    /**
     * @test
     */
    public function returnsProperPriceValue()
    {
        $this->assertEquals(
            27.10,
            $this->sut->parse($this->filteredData)->getValue()
        );
    }

    /**
     * @test
     */
    public function returnsProperPriceIdentifier()
    {
        $this->assertEquals(
            Carbon::create(2016,10,25,0,0,0,'Europe/Warsaw'),
            $this->sut->parse($this->filteredData)->getIdentifier()
        );
    }

    protected function setUp()
    {
        $prophet = new Prophet();
        $this->companyRepository = $prophet->prophesize(EntityRepository::class);
        $pgn = new Company('PGNiG', 'PGN', Type::ORDINARY, 'PGNIG');
        $this->companyRepository->findOneBy(['marketId' => 'PGN'])->willReturn($pgn);
        $this->companyRepository->findOneBy(['longMarketId' => 'PGNIG'])->willReturn($pgn);

        $this->sut = new BossaParser($this->companyRepository->reveal());
        $this->filteredData = new FilteredData("PGNIG,20161025,26.55,27.28,26.55,27.10,2431225");
    }
}