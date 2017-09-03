<?php

namespace Report\Entity\Report;

use Carbon\Carbon;

class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function translateFirstQuarter()
    {
        $identifier = Period::getIdentifierForYearAndQuarter('2015', 1);

        $this->assertEquals(Carbon::createFromFormat("Y-m-d", '2015-03-31', 'Europe/Warsaw'), $identifier);
    }
    
    /**
     * @test
     */
    public function translateSecondQuarter()
    {
        $identifier = Period::getIdentifierForYearAndQuarter('2015', '2');
    
        $this->assertEquals(Carbon::createFromFormat("Y-m-d", '2015-06-30', 'Europe/Warsaw'), $identifier);
    }
    
    /**
     * @test
     */
    public function translateThirdQuarter()
    {
        $identifier = Period::getIdentifierForYearAndQuarter(date('Y'), 3);
    
        $this->assertEquals(Carbon::createFromFormat("Y-m-d", date('Y').'-09-30', 'Europe/Warsaw'), $identifier);
    }
    
    /**
     * @test
     */
    public function translateFourthQuarter()
    {
        $identifier = Period::getIdentifierForYearAndQuarter('2014', '4');
    
        $this->assertEquals(Carbon::createFromFormat("Y-m-d", '2014-12-31', 'Europe/Warsaw'), $identifier);
    }
}