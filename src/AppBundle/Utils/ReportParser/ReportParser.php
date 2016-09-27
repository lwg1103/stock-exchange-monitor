<?php 
namespace AppBundle\Utils\ReportParser;

use AppBundle\Utils\ReportParser\ReportParserInterface;
use AppBundle\Entity\Company;

abstract class ReportParser { //implements ReportParserInterface {
	
	protected $company;
	abstract function parse(Company $company);
}