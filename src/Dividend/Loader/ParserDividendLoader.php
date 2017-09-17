<?php

namespace Dividend\Loader;

use Dividend\Entity\Dividend;

/**
 * Class ParserDividendLoader
 *
 * @package Dividend\Loader
 */
class ParserDividendLoader extends DividendLoader
{
    public function loadDividend(Dividend $dividend) {
        if($this->loadOrReplaceDividend($dividend)) {
            $this->load($dividend);
        }
    }

    private function loadOrReplaceDividend(Dividend $dividend)
    {
        $storedDividend = $this->em->getRepository('DividendContext:Dividend')->findOneBy([
            'company' => $dividend->getCompany(),
            'periodFrom' => $dividend->getPeriodFrom(),
            'periodTo' => $dividend->getPeriodTo()
        ]);

        if( (null != $storedDividend) ) {
            $storedDividend->setValue($dividend->getValue());
            $storedDividend->setRate($dividend->getRate());
            $storedDividend->setState($dividend->getState());
            $storedDividend->setPaymentDate($dividend->getPaymentDate());
            $storedDividend->setAgmDate($dividend->getAgmDate());
            
            $this->load($storedDividend);
        }
        else {
            $this->load($dividend);
        }
    }
}