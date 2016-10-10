<?php 

namespace AppBundle\Utils\ReportParser;

use Company\Entity\Company;

interface ReportParserInterface {
	
	public function parse(Company $company);
	
}