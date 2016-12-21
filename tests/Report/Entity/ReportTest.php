<?php

namespace Report\Entity;

class ReportTest extends \PHPUnit_Framework_TestCase
{
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
        $this->sut->setIdentifier(new \DateTime('31-12-2015'))
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