<?php

namespace Dividend\Entity;

use Carbon\Carbon;
use Company\Entity\Company;
use Dividend\Reader\ParserDividendReader;

class DividendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dividend
     */
    private $sut;

    /**
     * @test
     */
    public function setsState()
    {
        $this->sut->setState(Dividend\State::PROPOSAL);

        $this->assertEquals(Dividend\State::PROPOSAL, $this->sut->getState());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function throwsExceptionIfPeriodIsInvalid()
    {
        $this->sut->setState(13133);
    }

    /**
     * @test
     */
    public function canBeConvertedToString()
    {
        $company = new Company("Test Company Sp. z o.o.", "TST");
        
        $this->sut->setPeriodFrom(Carbon::createFromFormat("Y-m-d", '2017-01-01', ParserDividendReader::TIMEZONE)->setTime(0,0,0))
            ->setPeriodTo(Carbon::createFromFormat("Y-m-d", '2017-12-31', ParserDividendReader::TIMEZONE)->setTime(0,0,0))
            ->setState(Dividend\State::PAID)
            ->setValue(123)
            ->setCompany($company)
            ->setCurrency('PLN')
            ->setRate(3.45)
            ->setPaymentDate(Carbon::createFromFormat("Y-m-d", '2017-10-31', ParserDividendReader::TIMEZONE)->setTime(0,0,0));

        $expected = "TST (2017-01-01 - 2017-12-31) paid (2017-10-31): 1.23 (3.45%)";

        $this->assertEquals($expected, (string) $this->sut);
    }

    protected function setUp()
    {
        $this->sut = new Dividend();
    }
}