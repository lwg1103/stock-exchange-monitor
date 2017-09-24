<?php

namespace Dividend\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Company\Entity\Company\Type;
use Dividend\Entity\Dividend\State;
use Dividend\Parser\Stockwatch\StockwatchParser;
use Dividend\Reader\ParserDividendReader;
use Report\Entity\Report;
use Application\UseCase\ListCompanies;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class StockwatchParserTest extends KernelTestCase
{
    private $sut;


    
    /**
     * @test
     */
    public function checkDividendsCount() {
        $parsedDividends = $this->sut->parseYear(2016);
        $this->assertEquals(1, count($parsedDividends)); //got only one company for listCompanies here
    }
    
    /**
     * @test
     */
    public function checkDividendsForProphesizedComapny() {
        $parsedDividends = $this->sut->parseYear(2016);
        $checkedDividend = $parsedDividends[0];
        $this->assertEquals('PGN', $checkedDividend->getCompany()->getMarketId());
        $this->assertEquals(Carbon::createFromFormat("Y-m-d", '2016-01-01', ParserDividendReader::TIMEZONE)->setTime(0,0,0), $checkedDividend->getPeriodFrom());
        $this->assertEquals(Carbon::createFromFormat("Y-m-d", '2016-12-31', ParserDividendReader::TIMEZONE)->setTime(0,0,0), $checkedDividend->getPeriodTo());
        $this->assertEquals(20, $checkedDividend->getValue());
        $this->assertEquals(State::PAID, $checkedDividend->getState());
        $this->assertEquals(2.92, $checkedDividend->getRate());
    }

    protected function setUp()
    {
    	self::bootKernel();
    	
    	$prophet = new Prophet();
    	$listCompanies  = $prophet->prophesize(ListCompanies::class);
    	$listCompanies->execute()->willReturn([new Company('PGNiG', 'PGN', Type::ORDINARY, "PGNIG")]);
    	$parserDividendReader  = new ParserDividendReader();
    	$this->sut = new StockwatchParser($listCompanies->reveal(), $parserDividendReader);
    }

}