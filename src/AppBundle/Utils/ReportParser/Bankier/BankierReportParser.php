<?php

namespace AppBundle\Utils\ReportParser\Bankier;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use Company\Entity\Company;

class BankierReportParser extends ReportParser {

    public function parse(Company $company) {
        $this->company = $company;
        $html = $this->getData();
        $data = array();
        $data['company'] = $company;
        $data['identifier'] = new \DateTime('2015-06-30');
        $data['income'] = '12';
        $data['netProfit'] = '13';
        $data['operationalNetProfit'] = '';
        $data['bookValue'] = '15';
        $data['sharesQuantity'] = '16';
        $data['assets'] = '17';
        $report = $this->prepareReport($data);
        $this->em->persist($report);
        $this->em->flush();
    }


    private function getReportUrl() {
        return "http://www.bankier.pl/gielda/notowania/akcje/".$this->company->getMarketId()."/wyniki-finansowe";
    }

    private function getData() {

        try {
            $html = file_get_contents($this->getReportUrl());
        } catch (ContextErrorException $e) {
            $html = '';
        }

        return $html;
    }
}