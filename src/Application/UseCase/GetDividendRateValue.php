<?php

namespace Application\UseCase;

use Carbon\Carbon;
use Company\Entity\Company;
use Dividend\Entity\Dividend;
use phpDocumentor\Reflection\Types\This;

class GetDividendRateValue
{
    const NO_DATA_RESULT = -1;

    /** @var GetDividend */
    private $getDividend;
    
    /**
     * GetCZValue constructor.
     * @param GetDividend $getDividend
     */
    public function __construct(GetDividend $getDividend)
    {
        $this->getDividend = $getDividend;
    }

    /**
     * @param Company $company
     *
     * @return float
     */
    public function getForLastSevenYears(Company $company)
    {
        /** @var Dividend[] $dividends */
        $dividends = $this->getDividend->allByCompany($company);

        if ((count($dividends) < 1)) {
            return self::NO_DATA_RESULT;
        }

        $rate = 0;

        $counter = count($dividends) > 7 ? 7 : count($dividends);
        for ($i=0; $i < $counter; $i++ ) {
            $dividend = $dividends[$i];
            if (!$this->isFullYear($dividend)) {
                return self::NO_DATA_RESULT;
            }
            $rate += $dividend->getRate();
        }

        $rate /= 7;

        return round($rate, 2);
    }

    private function isFullYear(Dividend $dividend)
    {
        return $dividend->getPeriodFrom()->diff($dividend->getPeriodTo())->days >= 364;
    }
}