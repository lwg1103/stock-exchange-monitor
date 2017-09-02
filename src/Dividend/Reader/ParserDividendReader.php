<?php

namespace Dividend\Reader;


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
        $dividend = new Report();

        $dividend->setCompany($input['company']);
        $dividend->setIdentifier($input['identifier']);
        $dividend->setIncome($input['income']);
        $dividend->setAssets($input['assets']);
        $dividend->setLiabilities($input['liabilities']);
        $dividend->setNetProfit($input['netProfit']);
        $dividend->setOperationalNetProfit($input['operationalNetProfit']);
        $dividend->setBookValue($input['bookValue']);
        $dividend->setSharesQuantity($input['sharesQuantity']);
        $dividend->setCurrentAssets($input['currentAssets']);
        $dividend->setCurrentLiabilities($input['currentLiabilities']);

        $dividend->setType(Type::AUTO);
        $dividend->setPeriod(Period::ANNUAL);

        return $dividend;
    }

}