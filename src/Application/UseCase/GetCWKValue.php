<?php

namespace Application\UseCase;

use Company\Entity\Company;

class GetCWKValue
{
    const NO_DATA_RESULT = -1;
    const INFLECTION_POINT = 1.5;
    const CLASS_CHEAP = 'green';
    const CLASS_EXPENSIVE = 'red';
    
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
     * 
     * @param Company $company
     * 
     * @return string HTML
     */
    public function getCurrentFormatted(Company $company)
    {
        $price = $this->getCurrent($company);

        return $this->formatPrice($price);
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

    /**
     * @param Company $company
     *
     * @return string HTML
     */
    public function getForLastYearFormatted(Company $company)
    {
        $price = $this->getForLastYear($company);

        return $this->formatPrice($price);
    }

    private function calculateResult($totalCompanyPrice, $bookValue)
    {
        if((float)$bookValue == 0) {
            return self::NO_DATA_RESULT;
        }
        
        return round($totalCompanyPrice/($bookValue*1000), 2);
    }

    private function formatPrice($price)
    {
        return '<span class="price '.$this->getClassForPrice($price).'">'.$price.'</span>';
    }

    public function getClassForPrice($price)
    {
        $class = self::CLASS_CHEAP;
        if($price > self::INFLECTION_POINT) {
            $class = self::CLASS_EXPENSIVE;
        }

        return $class;
    }

}