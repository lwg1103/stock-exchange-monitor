<?php

namespace Report\Reader;

use Dividend\Entity\Dividend;

/**
 * Interface DividendReader
 *
 * @package Dividend\Reader
 */
interface DividendReader
{
    /**
     * @param mixed $input
     * 
     * @return Dividend
     */
    public function read($input);
}