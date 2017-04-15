<?php

namespace Price\Entity;

use Carbon\Carbon;
use Company\Entity\Company;
use Money\Currency;
use Money\Money;
use Prophecy\Prophet;
use Prophecy\Prophecy\ObjectProphecy;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Price
     */
    private $sut;
    /**
     * @var Company|ObjectProphecy
     */
    private $company;

    /**
     * @var Money|ObjectProphecy
     */
    private $money;
    /**
     * @var Currency|ObjectProphecy
     */
    private $currency;

    /**
     * @test
     */
    public function storesPrice()
    {
        $value = 50;
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);
        $this->whenNewObjectIsCreated();

        $this->thenValueIs($value);
        $this->thenCurrencyIs($currency);
    }

    /**
     * @test
     */
    public function isIdentifiedByCurrentDate()
    {
        $value = 50;
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);
        $this->whenNewObjectIsCreated();

        $this->thenIdentifierIsCurrentDate();
    }

    /**
     * @test
     *
     * @expectedException Money\InvalidArgumentException
     */
    public function raisesExceptionWhenWrongAmount()
    {
        $value = 'wat?';
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);

        $this->sut = new Price(
            $this->company->reveal(),
            new Money($value, $this->currency->reveal())
        );
    }

    /**
     * @test
     *
     * @expectedException Money\UnknownCurrencyException
     */
    public function raisesExceptionWhenUnknownCurrency()
    {
        $value = 50;
        $currency = 'monopoly_dollars';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);

        $this->sut = new Price(
            $this->company->reveal(),
            new Money($value, new Currency($currency))
        );
    }

    /**
     * @test
     */
    public function isConvertableToFormattedString()
    {
        $value = 150;
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);
        $this->whenNewObjectIsCreated();
        
        $this->assertEquals("1.50 PLN", (string)$this->sut);
    }

    /**
     * @test
     */
    public function isConvertableToFormattedStringWithTwoDigits()
    {
        $value = 200;
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);
        $this->whenNewObjectIsCreated();

        $this->assertEquals("2.00 PLN", (string)$this->sut);
    }

    /**
     * @test
     */
    public function isConvertableToFormattedStringForAmountBelowOne()
    {
        $value = 25;
        $currency = 'PLN';

        $this->whenMoneyValueIs($value);
        $this->whenMoneyCurrencyIs($currency);
        $this->whenNewObjectIsCreated();

        $this->assertEquals("0.25 PLN", (string)$this->sut);
    }

    private function whenMoneyValueIs($value)
    {
        $this->money->getAmount()->willReturn($value);
    }

    private function whenMoneyCurrencyIs($currency)
    {
        $this->currency->getName()->willReturn($currency);
        $this->money->getCurrency()->willReturn($this->currency->reveal());
    }

    private function whenNewObjectIsCreated()
    {
        $this->sut = new Price($this->company->reveal(), $this->money->reveal());
    }

    private function thenValueIs($value)
    {
        $this->assertEquals($value, $this->sut->getPrice()->getAmount());
    }

    private function thenCurrencyIs($currency)
    {
        $this->assertEquals($currency, $this->sut->getPrice()->getCurrency()->getName());
    }

    private function thenIdentifierIsCurrentDate()
    {
        $this->assertEquals(Carbon::today('Europe/Warsaw'), $this->sut->getIdentifier());
    }

    protected function setUp()
    {
        $prophet        = new Prophet();

        $this->company  = $prophet->prophesize(Company::class);
        $this->money    = $prophet->prophesize(Money::class);
        $this->currency = $prophet->prophesize(Currency::class);
    }

}