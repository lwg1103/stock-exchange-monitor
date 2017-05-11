<?php

namespace Application\UseCase;

use Report\Parser;

/**
 * Class ParseReport
 * 
 * @package AppBundle\UseCase
 */
class ParseReport
{
    /** @var Parser */
    private $parser;

    /**
     * PullPrices constructor.
     * @param EntityManager $entityManager
     * @param Downloader $downloader
     * @param Filter $filter
     * @param Parser $parser
     */
    public function __construct(
        Parser $parser
    )
    {
        $this->parser        = $parser;
    }
    
    public function parseReport($marketId) {
    	$this->getContainer()->get('app.use_case.get_company')->byMarketId($marketId);
    	
    	$report = $this->parseReportForCompany($company);
    }

    public function parseReportForCompany($company)
    {
    	$report = $this->parser->parse($company);
    	
    	return $report;
    }
}