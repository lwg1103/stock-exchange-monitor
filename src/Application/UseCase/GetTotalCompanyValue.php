<?php

namespace Application\UseCase;

use Company\Entity\Company;
use Doctrine\ORM\NoResultException;

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
        try {
            $currentPrice = $this->getPriceUseCase->lastByCompany($company)->getValue();
        } catch (NoResultException $e) {
            $currentPrice = 0;
        }
        
        try {
            $sharesQuantity = $this->getReportUseCase->lastByCompany($company)->getSharesQuantity();
        } catch (NoResultException $e) {
            $sharesQuantity = 0;
        }
        
        
        
        return $currentPrice*$sharesQuantity;
    }
}