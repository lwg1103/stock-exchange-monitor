<?php

namespace Application\Twig;

use Application\Twig\ApplicationExtension;

class ApplicationExtensionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function indicatorAboveSwitchPoint() {
        $expected = "<span class=\"indicator red\">2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(2, 1.5));
    }

    /**
     * @test
     */
    public function indicatorBelowSwitchPoint() {
        $expected = "<span class=\"indicator green\">-2</span>";
        $this->assertEquals($expected, $this->sut->coloredIndicatorFilter(-2, 1.5));
    }

    /**
     * @test
     */
    public function priceFormatted() {
        $expected = '<span class="price">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->priceFilter(2012.12));
    }

    /**
     * @test
     */
    public function priceFormattedAndColored() {
        $expected = '<span class="price red">2 012.12 zł</span>';
        $this->assertEquals($expected, $this->sut->coloredPriceFilter(2012.12, 15));
    }

    protected function setUp()
    {
        $this->sut = new ApplicationExtension();
    }
}