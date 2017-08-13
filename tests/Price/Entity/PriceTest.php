<?php

namespace Price\Entity;

use Carbon\Carbon;
use Company\Entity\Company;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Price
     */
    private $sut;
    /**
     * @var Company
     */
    private $company;

    /**
     * @var float
     */
    private $value;

    /**
     * @test
     */
    public function storesPrice()
    {
        $value = 50;

        $this->whenValueIs($value);
        $this->whenNewObjectIsCreated();

        $this->thenValueIs($value);
    }

    /**
     * @test
     */
    public function isIdentifiedByCurrentDate()
    {
        $value = 50;

        $this->whenValueIs($value);
        $this->whenNewObjectIsCreated();

        $this->thenIdentifierIsCurrentDate();
    }

    /**
     * @test
     */
    public function isConvertableToFormattedString()
    {
        $value = 150;

        $this->whenValueIs($value);
        $this->whenNewObjectIsCreated();
        
        $this->assertEquals("150.00", (string)$this->sut);
    }

    /**
     * @test
     */
    public function isConvertableToFormattedStringWithTwoDigits()
    {
        $value = 200;
        $this->whenValueIs($value);
        $this->whenNewObjectIsCreated();

        $this->assertEquals("200.00", (string)$this->sut);
    }

    /**
     * @test
     */
    public function isConvertableToFormattedStringForAmountBelowOne()
    {
        $value = 0.25;

        $this->whenValueIs($value);
        $this->whenNewObjectIsCreated();

        $this->assertEquals("0.25", (string)$this->sut);
    }

    private function whenValueIs($value)
    {
        $this->value = $value;
    }

    private function whenNewObjectIsCreated()
    {
        $this->sut = new Price($this->company, $this->value);
    }

    private function thenValueIs($value)
    {
        $this->assertEquals($value, $this->sut->getValue());
    }

    private function thenIdentifierIsCurrentDate()
    {
        $this->assertEquals(Carbon::today('Europe/Warsaw'), $this->sut->getIdentifier());
    }

    protected function setUp()
    {
        $this->company  = new Company("test", "test");
    }

}