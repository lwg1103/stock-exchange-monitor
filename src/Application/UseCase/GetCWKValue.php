<?php

namespace Application\UseCase;

use Company\Entity\Company;

class GetCWKValue
{
    const NO_DATA_RESULT = -1;
    
    /** @var GetReport */
    private $getReportUseCase;
    /** @var GetTotalCompanyValue */
    private $getTotalCompanyValue;

    /**
     * GetCWKValue constructor.
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
        $bookValue = $this->getReportUseCase->lastByCompany($company)->getBookValue();

        return $this->calculateResult($totalCompanyPrice, $bookValue);
    }

    /**
     * @param Company $company
     *
     * @return float
     */
    public function getForLastYear(Company $company)
    {
        $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        $bookValue = $this->getReportUseCase->lastYearByCompany($company)->getBookValue();
        
        return $this->calculateResult($totalCompanyPrice, $bookValue);
    }

    private function calculateResult($totalCompanyPrice, $bookValue)
    {
        if((float)$bookValue == 0) {
            return self::NO_DATA_RESULT;
        }
        
        return round($totalCompanyPrice/($bookValue*1000), 2);
    }
}