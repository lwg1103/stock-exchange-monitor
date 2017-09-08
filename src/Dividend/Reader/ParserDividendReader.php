<?php

namespace Dividend\Reader;

use Carbon\Carbon;
use Dividend\Entity\Dividend;

/**
 * Class ParserDividendReader
 *
 * Reads array of strings and returns Dividend
 */
class ParserDividendReader implements DividendReader
{
    /**
     * {@inheritdoc}
     */
    public function read($input)
    {
        $dividend = new Dividend();

        $dividend->setCompany($input['company']);
        $dividend->setPeriodFrom(Carbon::createFromFormat("Y-m-d", $input['period_from'], 'Europe/Warsaw'));
        $dividend->setPeriodTo(Carbon::createFromFormat("Y-m-d", $input['period_to'], 'Europe/Warsaw'));
        $dividend->setValue($input['value']);
        $dividend->setCurrency($input['currency']);
        $dividend->setRate($input['rate']);
        $dividend->setState($input['state']);
        if($input['payment_date']) {
            $dividend->setPaymentDate(Carbon::createFromFormat("Y-m-d", $input['payment_date'], 'Europe/Warsaw'));
        }
        if($input['agm_date']) {
            $dividend->setAgmDate(Carbon::createFromFormat("Y-m-d", $input['agm_date'], 'Europe/Warsaw'));
        }

        return $dividend;
    }

}