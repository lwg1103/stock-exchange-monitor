<?php

namespace Application\UseCase;

use Company\Entity\Company;

class GetNoLossValue
{
    const NO_DATA_RESULT = -1;
    
    /** @var GetReport */
    private $getReportUseCase;

    /**
     * GetCZValue constructor.
     * @param GetReport $getReportUseCase
     */
    public function __construct(GetReport $getReportUseCase)
    {
        $this->getReportUseCase = $getReportUseCase;
    }
    
    public function getForLastSevenYears(Company $company)
    {
        $lastYearsReports = $this->getReportUseCase->lastYearsByCompany($company);

        if(count($lastYearsReports) < 7) {
            return self::NO_DATA_RESULT;
        }

        $noLoss = true;

        for ($i=0; $i < 7; $i++ ) {
            if (($lastYearsReports[$i])->getNetProfit() < 0) {
                $noLoss = false;
                break;
            }
        }

        return $noLoss;
    }
}