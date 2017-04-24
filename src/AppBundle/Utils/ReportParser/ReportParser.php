<?php
namespace AppBundle\Utils\ReportParser;

use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;
use Report\Entity\Report\Type;
use Report\Entity\Report\Period;
use Report\Reader\ReportReader;
use Report\Loader\ReportLoader;
use Monolog\Logger;


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
}