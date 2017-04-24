<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityManager;
use Price\Entity\Price;
use Price\Downloader;
use Price\Filter;
use Price\Parser;

/**
 * Class GetPrice
 * 
 * @package AppBundle\UseCase
 */
class ParseReport
{
    /** @var EntityManager */
    private $entityManager;
    /** @var Downloader */
    private $downloader;
    /** @var Filter */
    private $filter;
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
        EntityManager       $entityManager, 
        Downloader          $downloader,
        Filter              $filter,
        Parser              $parser
    )
    {
        $this->entityManager        = $entityManager;
        $this->downloader           = $downloader;
        $this->filter               = $filter;
        $this->parser               = $parser;
    }
    
    public function parseReport($marketId) {
    	$this->getContainer()->get('app.use_case.get_company')->byMarketId($marketId);
    	
    	$this->parseReportForCompany($company);
    }

    public function parseReportForCompany($company)
    {
    	$reportParser = $this->getContainer()->get('app.utils.report_parser');
    	$report = $reportParser->parse($company);
    }

    protected function saveReports($reports) {
    	$this->log('[S] saving reports');
    	foreach($reports as $report) {
    		$objReport = $this->reader->read($report);
    		if($this->needStoreReport($objReport)) {
    			$this->loader->load($objReport);
    		}
    	}
    	$this->log('[E] saving reports');
    }
    
    public function needStoreReport($report) {
    	$this->log('checking report needs to be stored: '.$report->getCompany()->getMarketId(). " " . $report->getIdentifier());
    	
    	$storedReport = $this->er->findOneBy([
    			'company' => $report->getCompany(),
    			'identifier' => $report->getIdentifier(),
    			'period' => $report->getPeriod(),
    			'type' => Report\Type::AUTO
    	]);
    	
    	$this->log('check result: '.$storedReport);
    	
    	return !(null != $storedReport);
    }
}