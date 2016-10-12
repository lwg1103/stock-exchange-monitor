<?php

namespace AppBundle\Utils\ReportParser\Bankier;

use Symfony\Component\Debug\Exception\ContextErrorException;
use AppBundle\Utils\ReportParser\ReportParser;
use Company\Entity\Company;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;

class BankierReportParser extends ReportParser {

    public function parse(Company $company) {
        $this->company = $company;
        $html = $this->getData();
        //$dom = \Phpsimpledom\str_get_html($html);
        $cssConverter = new CssSelectorConverter();
        $path = $cssConverter->toXPath('div.box615.boxBlue.boxTable.left > div');
        echo $path;
        $dom = new Crawler($html);
        
        $dom = $dom->filter($path);
        echo count($dom);die;
        $data = array();
        $data['company'] = $company;
        $data['identifier'] = new \DateTime('2015-06-30');
        $data['income'] = '12';
        $data['netProfit'] = '13';
        $data['operationalNetProfit'] = '';
        $data['bookValue'] = '15';
        $data['sharesQuantity'] = '16';
        $data['assets'] = '17';
        //$report = $this->prepareReport($data);
        //$this->em->persist($report);
        //$this->em->flush();
    }


    private function getReportUrl() {
    	return "http://www.bankier.pl/gielda/notowania/akcje/PZU"."/wyniki-finansowe";
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