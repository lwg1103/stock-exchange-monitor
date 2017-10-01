<?php

use Behat\Behat\Context\Context;
use Report\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Price\Entity\Price;
use Application\UseCase\GetTotalCompanyValue;
use Application\UseCase\GetCZValue;

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

    /**
     * ReportContext constructor.
     *
     * @param GetTotalCompanyValue  $getTotalCompanyValue
     * @param GetCZValue            $getCZValue
     * @param ObjectManager         $em
     */
    public function __construct(
        GetTotalCompanyValue    $getTotalCompanyValue,
        GetCZValue              $getCZValue,
        ObjectManager           $em
    )
    {
        $this->getTotalCompanyValue = $getTotalCompanyValue;
        $this->getCZValue           = $getCZValue;
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
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getPriceRepository()
    {
        return $this->em->getRepository(Price::class);
    }

    private function getCompany($marketId)
    {
        return $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
    }
}
