<?php
namespace AppBundle\Utils\ReportParser;

use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;
use Report\Entity\Report\Type;
use Report\Entity\Report\Period;
use Report\Reader\ReportReader;
use Report\Loader\ReportLoader;


abstract class ReportParser { //implements ReportParserInterface {

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
     * Loader constructor.
     * @param ReportReader $reader
     * @param ReportLoader $loader
     */
    public function __construct(EntityRepository $er, ReportReader $reader, ReportLoader $loader)
    {
    	$this->er = $er;
    	$this->reader = $reader;
    	$this->loader = $loader;
    }
    
    
    
    /**
     * 
     * @param Company $company
     * 
     * return Report
     */
    abstract public function parse(Company $company);
    
    protected function saveReports($reports) {
    	
    	foreach($reports as $report) {
    		$objReport = $this->reader->read($report);
    		if($this->needStoreReport($objReport)) {
    			$this->loader->load($objReport);
    		}
    	}
    }
    
    protected function needStoreReport($report) {
    	$storedReport = $this->er->findOneBy([
    			'company' => $report->getCompany(),
    			'identifier' => $report->getIdentifier(),
    			'period' => $report->getPeriod(),
    			'type' => Report\Type::AUTO
    	]);
    	
    	return !(null != $storedReport);
    }
}