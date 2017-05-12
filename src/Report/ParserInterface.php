<?php 

namespace Report;

use Company\Entity\Company;

interface ParserInterface {

    public function parse(Company $company);

}