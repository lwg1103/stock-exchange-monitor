<?php

namespace Report\Entity\Report;

use Application\BaseEnum;
use Carbon\Carbon;

/**
 * Class Period
 * 
 * @package AppBundle\Entity\Report
 */
class Period extends BaseEnum
{
    const ANNUAL = 1;
    const BIANNUAL = 2;
    const QUARTERLY = 4;
    
    const FIRST_QUARTER = 1;
    const SECOND_QUARTER = 2;
    const THIRD_QUARTER = 3;
    const FOURTH_QUARTER = 4;
    
    const FIRST_QUARTER_IDENTIFIER = '-03-31';
    const SECOND_QUARTER_IDENTIFIER = '-06-30';
    const THIRD_QUARTER_IDENTIFIER = '-09-30';
    const FOURTH_QUARTER_IDENTIFIER = '-12-31';
    
    public static function getDateStringForYearAndQuarter($year, $quarter) {
        $year = (int)$year;
        //1531 - rok otwarcia pierwszej giełdy na swiecie (z wiki) - używam do sprawdzenia czy nie są bzdury wrzucone
        if($year < 1531) {
            throw new Exception('Wrong year during conversion to identifier');
        }
        
        $qIdentifier = false;
        switch($quarter) {
            case self::FIRST_QUARTER:
                $qIdentifier = self::FIRST_QUARTER_IDENTIFIER;
                break;
            case self::SECOND_QUARTER:
                $qIdentifier = self::SECOND_QUARTER_IDENTIFIER;
                break;
            case self::THIRD_QUARTER:
                $qIdentifier = self::THIRD_QUARTER_IDENTIFIER;
                break;
            case self::FOURTH_QUARTER:
                $qIdentifier = self::FOURTH_QUARTER_IDENTIFIER;
                break;
        }
        
        if(!$qIdentifier) {
            throw new \Exception('Wrong quarter during conversion to identifier');
        }
        
        return $year.$qIdentifier;
    }
    
    public static function getIdentifierForYearAndQuarter($year, $quarter) {
        $identifierDateString = self::getDateStringForYearAndQuarter($year, $quarter);
        return Carbon::createFromFormat("Y-m-d", $identifierDateString, 'Europe/Warsaw');
    }
}