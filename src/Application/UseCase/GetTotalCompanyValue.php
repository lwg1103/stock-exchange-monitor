<?php

namespace Application\UseCase;

use Company\Entity\Company;
use Money\Money;

class GetTotalCompanyValue
{
    /** @var GetPrice */
    private $getPriceUseCase;
    /** @var GetReport */
    private $getReportUseCase;

    /**
     * GetTotalCompanyValue constructor.
     * @param \Application\UseCase\GetPrice $getPriceUseCase
     * @param \Application\UseCase\GetReport $getReportUseCase
     */
    public function __construct(GetPrice $getPriceUseCase, GetReport $getReportUseCase)
    {
        $this->getPriceUseCase = $getPriceUseCase;
        $this->getReportUseCase = $getReportUseCase;
    }

    /**
     * @param Company $company
     * 
     * @return float
     */
    public function get(Company $company)
    {
        $currentPrice = $this->getPriceUseCase->lastByCompany($company)->getValue();
        $sharesQuantity = $this->getReportUseCase->lastByCompany($company)->getSharesQuantity();
        
        return $currentPrice*$sharesQuantity;
    }
}