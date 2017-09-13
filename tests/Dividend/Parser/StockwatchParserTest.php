<?php

namespace Dividend\Parser;

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
        $this->assertEquals(count($parsedDividends), 1); //got only one company for listCompanies here
    }
    
    /**
     * @test
     */
    public function checkDividendsForProphesizedComapny() {
        $parsedDividends = $this->sut->parseYear(2016);
        $checkedDividend = $parsedDividends[0];
        $this->assertEquals($checkedDividend->getCompany()->getMarketId(), 'PGN');
        $this->assertEquals($checkedDividend->getPeriodFrom(), new \DateTime('2016-01-01'));
        $this->assertEquals($checkedDividend->getPeriodTo(), new \DateTime('2016-12-31'));
        $this->assertEquals($checkedDividend->getValue(), 20);
        $this->assertEquals($checkedDividend->getState(), State::PAID);
        $this->assertEquals($checkedDividend->getRate(), 2.92);
    }
    
    private function getStoredQuarterlyReport($company, $year, $quarter) {
        $storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
            'company' => $company,
            'identifier' => Report\Period::getIdentifierForYearAndQuarter($year, $quarter),
            'period' => Report\Period::QUARTERLY,
            'type' => Report\Type::AUTO
        ]);
    
        return $storedReport;
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