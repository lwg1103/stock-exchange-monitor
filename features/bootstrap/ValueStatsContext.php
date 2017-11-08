<?php

use Behat\Behat\Context\Context;
use Report\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Price\Entity\Price;
use Application\UseCase\GetTotalCompanyValue;
use Application\UseCase\GetCZValue;
use Application\UseCase\GetCWKValue;
use Application\UseCase\GetNoLossValue;

/**
 * Defines application features from the specific context.
 */
class ValueStatsContext implements Context
{
    /** @var Money */
    protected $result;
    protected $currentCount;
    /** @var ObjectManager */
    protected $em;
    /** @var GetTotalCompanyValue */
    protected $getTotalCompanyValue;
    /** @var GetCZValue */
    protected $getCZValue;
    /** @var GetCWKValue */
    protected $getCWKValue;
    /** @var GetNoLossValue */
    protected $getNoLossValue;

    /**
     * ReportContext constructor.
     *
     * @param GetTotalCompanyValue  $getTotalCompanyValue
     * @param GetCZValue            $getCZValue
     * @param GetCWKValue           $getCWKValue
     * @param GetNoLossValue        $getNoLossValue
     * @param ObjectManager         $em
     */
    public function __construct(
        GetTotalCompanyValue    $getTotalCompanyValue,
        GetCZValue              $getCZValue,
        GetCWKValue             $getCWKValue,
        GetNoLossValue          $getNoLossValue,
        ObjectManager           $em
    )
    {
        $this->getTotalCompanyValue = $getTotalCompanyValue;
        $this->getCZValue           = $getCZValue;
        $this->getCWKValue          = $getCWKValue;
        $this->getNoLossValue       = $getNoLossValue;
        $this->em                   = $em;
    }

    /**
     * @Given :marketId company current price is :value
     */
    public function companyCurrentPriceIs($marketId, $value)
    {
        $company = $this->getCompany($marketId);

        $price = $this->em->getRepository('PriceContext:Price')->findOneBy([
            'company' => $company,
            'identifier' => \Carbon\Carbon::now()
        ]);

        if (!$price) {
            $price = new Price($company, $value);
            $this->em->persist($price);
        }

        $price->setValue($value);
        $this->em->flush();
    }

    /**
     * @When I check total company value for :marketId
     */
    public function iCheckTotalCompanyValue($marketId)
    {
        $this->result = $this->getTotalCompanyValue->get($this->getCompany($marketId));
    }

    /**
     * @Then I should see :value total company value
     */
    public function iShouldSeeTotalCompanyValue($value)
    {
        assertEquals($value, $this->result);
    }

    /**
     * @When I check C\/Z for last year value for :marketId
     */
    public function iCheckCZValueFor($marketId)
    {
        $this->result = $this->getCZValue->getForLastYear($this->getCompany($marketId));
    }

    /**
     * @When I check C\/Z for last 7 years value for :marketId
     */
    public function iCheckCZ7YValueFor($marketId)
    {
        $this->result = $this->getCZValue->getForLastSevenYears($this->getCompany($marketId));
    }

    /**
     * @When I check C\/Z for last 4Q value for :marketId
     */
    public function iCheckCZ4QValueFor($marketId)
    {
        $this->result = $this->getCZValue->getForLastFourQuarters($this->getCompany($marketId));
    }

    /**
     * @Then I should see :value C\/Z value
     */
    public function iShouldSeeCZValue($value)
    {
        assertEquals($value, $this->result);
    }

    /**
     * @Then I see it is :result that :marketId had no loss in net profit in last seven years
     */
    public function iSeeItSThatTheCompanyHadNoLossInNetProfitInLastYears($result, $marketId)
    {
        $expectedResult = ($result == "true") ? true : false;
        assertEquals($expectedResult, $this->getNoLossValue->getForLastSevenYears($this->getCompany($marketId)));
    }

    private function getCompany($marketId)
    {
        return $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
    }

    /**
     * @When I check C\/WK for last year value for :marketId
     */
    public function iCheckCWkForLastYearValueFor($marketId)
    {
        $this->result = $this->getCWKValue->getForLastYear($this->getCompany($marketId));
    }

    /**
     * @Then I should see :value C\/WK value
     */
    public function iShouldSeeCWkValue($value)
    {
        assertEquals($value, $this->result);
    }
}
