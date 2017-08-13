<?php

namespace Company\Entity\Company;

use Application\BaseEnum;

/**
 * Class Type
 * 
 * @package Company\Entity\Company
 */
class Type extends BaseEnum
{
    const ORDINARY = 1;
    const BANK = 2;
    
    public static function getTypes() {
    	return array(self::ORDINARY, self::BANK);
    }
}