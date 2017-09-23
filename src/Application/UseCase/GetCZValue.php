<?php

namespace Application\UseCase;

use Company\Entity\Company;
use Doctrine\ORM\NoResultException;

class GetCZValue
{
    const NO_DATA_RESULT = -1;
    
    /** @var GetReport */
    private $getReportUseCase;
    /** @var GetTotalCompanyValue */
    private $getTotalCompanyValue;

    /**
     * GetCZValue constructor.
     * @param GetReport $getReportUseCase
     * @param GetTotalCompanyValue $getTotalCompanyValue
     */
    public function __construct(GetReport $getReportUseCase, GetTotalCompanyValue $getTotalCompanyValue)
    {
        $this->getReportUseCase = $getReportUseCase;
        $this->getTotalCompanyValue = $getTotalCompanyValue;
    }

    /**
     * @param Company $company
     * 
     * @return float
     */
    public function getCurrent(Company $company)
    {
        $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        $netProfit = $this->getReportUseCase->lastByCompany($company)->getNetProfit();

        return $this->calculateResult($totalCompanyPrice, $netProfit);
    }

    /**
     * @param Company $company
     *
     * @return float
     */
    public function getForLastYear(Company $company)
    {
        try {
            $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        } catch (NoResultException $e) { 
            return self::NO_DATA_RESULT;
        }
        
        try {
            $netProfit = $this->getReportUseCase->lastYearByCompany($company)->getNetProfit();
        } catch (NoResultException $e) {
            return self::NO_DATA_RESULT;
        }

        return $this->calculateResult($totalCompanyPrice, $netProfit);
    }
    
    public function getForLastFourQuarters(Company $company)
    {
        try {
            $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        } catch (NoResultException $e) {
            return self::NO_DATA_RESULT;
        }
        
        try {
            $lastQuarterReports = $this->getReportUseCase->lastQuartersByCompany($company);
        } catch (NoResultException $e) {
            return self::NO_DATA_RESULT;
        }
        
        if(count($lastQuarterReports) < 4) {
            return self::NO_DATA_RESULT;
        }

        $netProfit = 0;

        for ($i=0; $i < 4; $i++ ) {
            $netProfit += ($lastQuarterReports[$i])->getNetProfit();
        }

        return $this->calculateResult($totalCompanyPrice, $netProfit);
    }

    private function calculateResult($totalCompanyPrice, $netProfit)
    {
        return round($totalCompanyPrice/($netProfit*1000), 2);
    }
}