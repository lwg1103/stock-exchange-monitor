<?php

namespace Application\UseCase;

use Company\Entity\Company;

class GetCZValue
{
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
        $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        $netProfit = $this->getReportUseCase->lastYearByCompany($company)->getNetProfit();

        return $this->calculateResult($totalCompanyPrice, $netProfit);
    }
    
    public function getForLastFourQuarters(Company $company)
    {
        $totalCompanyPrice = $this->getTotalCompanyValue->get($company);
        $lastQuarterReports = $this->getReportUseCase->lastQuartersByCompany($company);

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