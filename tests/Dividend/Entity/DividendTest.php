<?php

namespace Dividend\Entity;

use Company\Entity\Company;

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
        
        $this->sut->setPeriodFrom(new \DateTime('2017-01-01'))
            ->setPeriodTo(new \DateTime('2017-12-31'))
            ->setState(Dividend\State::PAID)
            ->setValue(123)
            ->setCompany($company)
            ->setCurrency('PLN');

        $expected = "TST (2017-01-01 - 2017-12-31) paid: 1.23 (PLN)";

        $this->assertEquals($expected, (string) $this->sut);
    }

    protected function setUp()
    {
        $this->sut = new Dividend();
    }
}