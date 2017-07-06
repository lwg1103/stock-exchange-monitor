<?php

namespace Application\UseCase;

use Dividend\Parser\Stockwatch\StockwatchParser;

use Report\Loader\ParserReportLoader;
/**
 * Class GetCompanyOnlineReports
 *
 * @package AppBundle\UseCase
 */
class GetOnlineDividendsForYear
{
    /** @var Parser */
    private $parser;

    /** @var ParserReportLoader */
    private $loader;

    /**
     * GetOnlineDividendsForYear constructor
     * @param Parser $parser
     */
    public function __construct(
        StockwatchParser $parser,
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
    public function parseDividendsForYear($year)
    {
        $reports = $this->parser->parseYear($year);

        return $reports;
    }
}