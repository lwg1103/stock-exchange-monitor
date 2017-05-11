<?php
namespace Report;

use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;
use Report\Reader\ReportReader;
use Report\Loader\ReportLoader;
use Monolog\Logger;


abstract class Parser implements ParserInterface {

	/**
	 * 
	 * @var Company
	 */
    protected $company;
    
    /**
     * 
     * @var EntityRepository
     */
    protected $er;
    
    /**
     * @var ReportLoader
     */
    protected $loader;
    
    /**
     * @var ReportReader
     */
    protected $reader;
    
    /**
     * @var html string
     */
    protected $html;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    /**
     * Loader constructor.
     * @param ReportReader $reader
     * @param ReportLoader $loader
     */
    public function __construct(EntityRepository $er, ReportReader $reader, ReportLoader $loader, Logger $logger)
    {
    	$this->er = $er;
    	$this->reader = $reader;
    	$this->loader = $loader;
    	$this->logger = $logger;
    }
    
    /**
     * 
     * @param Company $company
     * 
     * return Report
     */
    abstract public function parse(Company $company);
    
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
    	$this->log('checking report needs to be stored: '.$report->getCompany()->getMarketId(). " " . $report->getIdentifier()->format('Y-m-d'));
    
    	$storedReport = $this->er->findOneBy([
    			'company' => $report->getCompany(),
    			'identifier' => $report->getIdentifier(),
    			'period' => $report->getPeriod(),
    			'type' => Report\Type::AUTO
    	]);
    
    	$this->log('check result: '.$storedReport);
    
    	return !(null != $storedReport);
    }
    
    protected function log($message) {
    	$this->logger->info($message);
    	echo $message.'\n';
    }
}