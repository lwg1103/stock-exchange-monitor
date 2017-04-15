<?php

namespace Report\Reader;

use Report\Entity\Report;

/**
 * Interface ReportReader
 *
 * @package AppBundle\Reader
 */
interface ReportReader
{
    /**
     * @param mixed $input
     * 
     * @return Report
     */
    public function read($input);
}