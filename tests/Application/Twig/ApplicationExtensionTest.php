<?php

namespace Application\Twig;

use Application\Twig\ApplicationExtension;

class ApplicationExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function indicatorAboveSwitchPointIsRed()
    {
        $expected = "<span class=\"indicator red\">2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(2, 1.5));
    }

    /**
     * @test
     */
    public function indicatorBelowSwitchPointIsGreen()
    {
        $expected = "<span class=\"indicator green\">-2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(-2, 1.5));
    }

    /**
     * @test
     */
    public function indicatorAboveSwitchPointIsGreenIfReversed()
    {
        $expected = "<span class=\"indicator green\">2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(2, 1.5, true));
    }

    /**
     * @test
     */
    public function indicatorBelowSwitchPointIsRedIfReversed()
    {
        $expected = "<span class=\"indicator red\">-2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(-2, 1.5, true));
    }

    /**
     * @test
     */
    public function priceFormatted()
    {
        $expected = '<span class="price">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->priceFilter(2012.12));
    }

    /**
     * @test
     */
    public function priceFormattedAndColoredRedAboveSwitchPoint()
    {
        $expected = '<span class="price red">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->coloredPriceFilter(2012.12, 15));
    }

    /**
     * @test
     */
    public function priceFormattedAndColoredGreenBelowSwitchPoint()
    {
        $expected = '<span class="price green">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->coloredPriceFilter(2012.12, 3000));
    }

    /**
     * @test
     */
    public function priceFormattedAndColoredGreenAboveSwitchPointIfReversed()
    {
        $expected = '<span class="price green">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->coloredPriceFilter(2012.12, 15, true));
    }

    /**
     * @test
     */
    public function priceFormattedAndColoredRedBelowSwitchPointIfReversed()
    {
        $expected = '<span class="price red">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->coloredPriceFilter(2012.12, 3000, true));
    }

    /**
     * @test
     */
    public function noLossIsOk()
    {
        $expected = '<span class="indicator green">OK</span>';
        $this->assertEquals($expected, $this->sut->noLossFilter(1));
    }

    /**
     * @test
     */
    public function noLossFailed()
    {
        $expected = '<span class="indicator red">FAILED</span>';
        $this->assertEquals($expected, $this->sut->noLossFilter(0));
    }

    protected function setUp()
    {
        $this->sut = new ApplicationExtension();
    }
}