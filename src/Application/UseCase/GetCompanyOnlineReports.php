<?php

namespace Application\UseCase;

use Report\Parser;
use Report\Loader\ParserReportLoader;

/**
 * Class GetCompanyOnlineReports
 *
 * @package AppBundle\UseCase
 */
class GetCompanyOnlineReports
{
    /** @var Parser */
    private $parser;

    /** @var ParserReportLoader */
    private $loader;

    /**
     * GetCompanyOnlineReports constructor
     * @param Parser $parser
     */
    public function __construct(
        Parser $parser,
        ParserReportLoader $loader
    )
    {
        $this->parser = $parser;
        $this->loader = $loader;
    }

    /**
     * @param string $marketId
     * @return Report[]
     */
    public function parseLoadReport($company) {

        $reports = $this->parseReportsForCompany($company);

        foreach($reports as $report) {
            $this->loader->loadReportIfNeeded($report);
        }
    }

    /**
     *
     * @param Company $company
     * @return Report[]
     */
    public function parseReportsForCompany($company)
    {
        $reports = $this->parser->parse($company);

        return $reports;
    }
}