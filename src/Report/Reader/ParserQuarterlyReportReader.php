<?php

namespace Report\Reader;

use Report\Entity\Report;
use Report\Entity\Report\Period;

/**
 * Class FormReader
 *
 * Reads symfony form and returns Report
 */
class ParserQuarterlyReportReader extends ParserReportReader
{
    /**
     * {@inheritdoc}
     */
    public function read($input)
    {
        $report = parent::read($input);

        $report->setPeriod(Period::QUARTERLY);

        return $report;
    }

}
