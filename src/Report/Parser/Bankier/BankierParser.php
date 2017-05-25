<?php

namespace Report\Parser\Bankier;

use Symfony\Component\Debug\Exception\ContextErrorException;
use Report\Parser;
use Company\Entity\Company;
use Symfony\Component\DomCrawler\Crawler;

class BankierParser extends Parser {

    public function parse(Company $company) {
        //próbna implementacja parsera w historii repozytorium
        throw new \Exception('not implemented yet');
        $this->company = $company;
        $this->html = $this->getData();

        return $this->processParser();
    }
}