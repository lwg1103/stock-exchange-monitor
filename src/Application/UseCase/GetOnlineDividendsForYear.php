<?php

namespace Application\UseCase;

use Dividend\Loader\ParserDividendLoader;
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
        ParserDividendLoader $loader
    )
    {
        $this->parser = $parser;
        $this->loader = $loader;
    }

    /**
     * @param string $year
     * @return Dividend[]
     */
    public function parseLoadDividends($year) {

        $dividends = $this->parseDividendsForYear($year);
        
        foreach($dividends as $dividend) {
            $this->loader->loadDividend($dividend);
        }
    }


    /**
     *
     * @param Company $company
     * @return Dividend[]
     */
    private function parseDividendsForYear($year)
    {
        $dividends = $this->parser->parseYear($year);

        return $dividends;
    }
}