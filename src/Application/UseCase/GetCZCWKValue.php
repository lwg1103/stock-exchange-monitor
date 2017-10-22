<?php

namespace Application\UseCase;

use Company\Entity\Company;

class GetCZCWKValue
{
    private $getCZValue;
    private $getCWKValue;

    /**
     * GetCWKValue constructor.
     * @param GetCZValue $getCZValue
     * @param GetCWKValue $getCWKValue
     */
    public function __construct(GetCZValue $getCZValue, GetCWKValue $getCWKValue)
    {
        $this->getCZValue = $getCZValue;
        $this->getCWKValue = $getCWKValue;
    }

    /**
     * @param Company $company
     * 
     * @return float
     */
    public function getCurrent(Company $company)
    {
        $currentCZ = $this->getCZValue->getCurrent($company);
        $currentCWK = $this->getCWKValue->getCurrent($company);

        return $this->calculateResult($currentCZ, $currentCWK);
    }

    /**
     * @param Company $company
     *
     * @return float
     */
    public function getForLastYear(Company $company)
    {
        $lastYearCZ = $this->getCZValue->getForLastYear($company);
        $lastYearCWK = $this->getCWKValue->getForLastYear($company);

        return $this->calculateResult($lastYearCZ, $lastYearCWK);
    }

    private function calculateResult($cz, $cwk)
    {
        return round($cz * $cwk, 2);
    }
}