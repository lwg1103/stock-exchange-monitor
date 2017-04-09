<?php

use Behat\Behat\Context\Context;
use Report\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Price\Entity\Price;
use Money\Money;
use Money\Currency;
use Application\UseCase\GetTotalCompanyValue;

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

    /**
     * ReportContext constructor.
     *
     * @param GetTotalCompanyValue  $getTotalCompanyValue
     * @param ObjectManager         $em
     */
    public function __construct(
        GetTotalCompanyValue    $getTotalCompanyValue, 
        ObjectManager           $em
    )
    {
        $this->getTotalCompanyValue = $getTotalCompanyValue;
        $this->em                   = $em;
    }

    /**
     * @Given :marketId company current price is :price
     */
    public function companyCurrentPriceIs($marketId, $price)
    {
        $price = new Price(
            $this->getCompany($marketId),
            new Money((int)$price, new Currency('PLN'))
        );

        $this->em->persist($price);
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
        assertEquals($value, $this->result->getAmount());
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
