<?php

namespace AppBundle\Reader;

use AppBundle\Entity\Report;
use AppBundle\Entity\Report\Type;

/**
 * Class FormReader
 *
 * Reads symfony form and returns Report
 *
 * @package AppBundle\Reader
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