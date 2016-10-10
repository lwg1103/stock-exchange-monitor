<?php
namespace AppBundle\Utils\ReportParser;

use Company\Entity\Company;
use Doctrine\ORM\EntityManager;
use Report\Entity\Report;
use Report\Entity\Report\Type;
use Report\Entity\Report\Period;


abstract class ReportParser { //implements ReportParserInterface {

	/**
	 * 
	 * @var Company
	 */
    protected $company;
    
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * Loader constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
    	$this->em = $em;
    }
    
    
    
    /**
     * 
     * @param Company $company
     * 
     * return Report
     */
    abstract public function parse(Company $company);
    
    protected function prepareReport($data) {
    	$report = new Report();
    	$report->setCompany($data['company']);
    	$report->setIdentifier($data['identifier']);
    	$report->setType(Type::AUTO);
    	$report->setPeriod(Period::QUARTERLY);
    	$report->setIncome($data['income']);
    	$report->setAssets($data['assets']);
    	$report->setNetProfit($data['netProfit']);
    	$report->setOperationalNetProfit($data['operationalNetProfit']);
    	$report->setBookValue($data['bookValue']);
    	$report->setSharesQuantity($data['sharesQuantity']);
    	/*$report->set($data['']);
    	$report->set($data['']);
    	$report->set($data['']);
    	$report->set($data['']);
    	$report->set($data['']);
    	$report->set($data['']);
    	*/
    	return $report;
    }
}