<?php

namespace Company\Entity\Company\Group;

use Application\BaseEnum;

/**
 * Class Type
 * 
 * @package Company\Entity\Company\Group
 */
class Type extends BaseEnum
{
    const INDEX = 1;
    const INDUSTRY = 2;
    const SECTOR = 3;
    
    public static function getTypes() {
    	return array(self::INDEX, self::INDUSTRY, self::SECTOR);
    }
}