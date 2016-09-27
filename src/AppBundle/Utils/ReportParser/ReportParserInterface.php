<?php 

namespace AppBundle\Utils\ReportParser;

use AppBundle\Entity\Company;

interface ReportParserInterface {
	
	public function parse(Company $company);
	
}