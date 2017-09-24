<?php

namespace Report\Entity;

use Carbon\Carbon;

class ReportTest extends \PHPUnit_Framework_TestCase
{
    const TIMEZONE = 'Europe/Warsaw';

    /**
     * @var Report
     */
    private $sut;

    /**
     * @test
     */
    public function setsType()
    {
        $this->sut->setType(Report\Type::MANUAL);

        $this->assertEquals(Report\Type::MANUAL, $this->sut->getType());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function throwsExceptionIfTypeIsInvalid()
    {
        $this->sut->setType('trolollo');
    }

    /**
     * @test
     */
    public function setsPeriod()
    {
        $this->sut->setPeriod(Report\Period::ANNUAL);

        $this->assertEquals(Report\Period::ANNUAL, $this->sut->getPeriod());
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function throwsExceptionIfPeriodIsInvalid()
    {
        $this->sut->setPeriod('trolollo');
    }

    /**
     * @test
     */
    public function canBeConvertedToString()
    {
        $this->sut->setIdentifier(Carbon::createFromFormat("d-m-Y", '31-12-2015', self::TIMEZONE)->setTime(0,0,0))
            ->setType(Report\Type::MANUAL)
            ->setPeriod(Report\Period::ANNUAL);

        $expected = "31-12-2015 (annual, manual)";

        $this->assertEquals($expected, (string) $this->sut);
    }

    protected function setUp()
    {
        $this->sut = new Report();
    }
}