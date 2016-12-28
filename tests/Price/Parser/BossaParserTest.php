<?php

namespace Price\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Company\Translator\BossaMarketIdTranslator;
use Doctrine\ORM\EntityRepository;
use Price\Entity\Price;
use Price\Filter\FilteredData;
use Prophecy\Prophet;
use Symfony\Component\Validator\Constraints\DateTime;

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
            2710,
            $this->sut->parse($this->filteredData)->getPrice()->getAmount()
        );
    }

    /**
     * @test
     */
    public function returnsProperPriceCurrency()
    {
        $this->assertEquals(
            'PLN',
            $this->sut->parse($this->filteredData)->getPrice()->getCurrency()->getName()
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
        $this->companyRepository->findOneBy(['marketId' => 'PGN'])->willReturn(new Company('PGNiG', 'PGN'));

        $this->sut = new BossaParser($this->companyRepository->reveal(), new BossaMarketIdTranslator());
        $this->filteredData = new FilteredData("PGNIG,20161025,26.55,27.28,26.55,27.10,2431225");
    }
}