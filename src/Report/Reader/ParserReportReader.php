<?php

namespace Report\Reader;

use Report\Entity\Report;
use Report\Entity\Report\Type;
use Report\Entity\Report\Period;

/**
 * Class FormReader
 *
 * Reads symfony form and returns Report
 */
class ParserReportReader implements ReportReader
{
    /**
     * {@inheritdoc}
     */
    public function read($input)
    {
        $report = new Report();

        $report->setCompany($input['company']);
        $report->setIdentifier($input['identifier']);
        $report->setIncome($input['income']);
        $report->setAssets($input['assets']);
        $report->setLiabilities($input['liabilities']);
        $report->setNetProfit($input['netProfit']);
        $report->setOperationalNetProfit($input['operationalNetProfit']);
        $report->setBookValue($input['bookValue']);
        $report->setSharesQuantity($input['sharesQuantity']);
        $report->setCurrentAssets($input['currentAssets']);
        $report->setCurrentLiabilities($input['currentLiabilities']);

        $report->setType(Type::AUTO);
        $report->setPeriod(Period::ANNUAL);

        return $report;
    }

}