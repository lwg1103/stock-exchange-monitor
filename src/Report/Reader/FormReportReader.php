<?php

namespace Report\Reader;

use Report\Entity\Report;
use Report\Entity\Report\Type;

/**
 * Class FormReader
 *
 * Reads symfony form and returns Report
 */
class FormReportReader implements ReportReader
{
    /**
     * {@inheritdoc}
     */
    public function read($input)
    {
        $report = $input->getData();
        $report->setType(Type::MANUAL);

        return $report;
    }

}