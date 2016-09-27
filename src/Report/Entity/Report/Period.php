<?php

namespace Report\Entity\Report;

use Application\BaseEnum;

/**
 * Class Period
 * 
 * @package AppBundle\Entity\Report
 */
class Period extends BaseEnum
{
    const ANNUALLY = 1;
    const BIANNUAL = 2;
    const QUARTERLY = 4;
}