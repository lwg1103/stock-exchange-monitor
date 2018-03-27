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
    const SECTOR = 2;
    const INDUSTRY = 3;
    
    public static function getTypes() {
    	return array(self::INDEX, self::SECTOR, self::INDUSTRY);
    }
}