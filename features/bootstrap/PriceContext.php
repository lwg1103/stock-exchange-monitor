<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Application\UseCase\GetPrice;
use Application\UseCase\PullPrice;
use Application\UseCase\PullAllPrices;
use Price\Entity\Price;
use Company\Entity\Company;
use Carbon\Carbon;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class PriceContext implements Context
{
    /** @var  Price */
    protected $resultOne;
    /** @var  Price[] */
    protected $resultAll;
    /** @var ObjectManager */
    protected $em;
    /** @var GetPrice */
    private $getPrice;
    /** @var PullPrice */
    private $pullPrice;
    /** @var PullAllPrices */
    private $pullAllPrices;

    /**
     * CompanyContext constructor.
     *
     * @param GetPrice          $getPrice
     * @param PullPrice         $pullPrice
     * @param PullAllPrices     $pullAllPrices
     * @param ObjectManager     $em
     */
    public function __construct(
        GetPrice        $getPrice,
        PullPrice       $pullPrice,
        PullAllPrices   $pullAllPrices,
        ObjectManager   $em
    )
    {
        $this->getPrice         = $getPrice;
        $this->pullPrice        = $pullPrice;
        $this->pullAllPrices    = $pullAllPrices;
        $this->em               = $em;
    }

    /**
     * @Given There are no prices for :relativeTime
     */
    public function thereAreNoPricesFor($relativeTime)
    {
        $prices = $this->findAllPricesFor(new \Carbon\Carbon($relativeTime));

        foreach ($prices as $price)
            $this->em->remove($price);

        $this->em->flush();
    }

    /**
     * @Given There is price with value :value for :marketId for :relativeTime
     */
    public function thereIsPriceFor($value, $marketId, $relativeTime)
    {
        /** @var Company $company */
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        $price = new Price($company, $value, new \Carbon\Carbon($relativeTime));

        $this->em->persist($price);
        $this->em->flush();
    }

    /**
     * @When /^I enter "([^"]*)" company price site$/
     */
    public function iEnterCompanyPriceSite($marketId)
    {
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        $this->resultAll = $this->getPrice->allByCompany($company);
        $this->resultOne = $this->getPrice->lastByCompany($company);
    }

    /**
     * @Then /^I see one current company price$/
     */
    public function iSeeLastCompanyPrice()
    {        
        assertInstanceOf(Price::class, $this->resultOne);
    }

    /**
     * @Then The current company price is :value
     */
    public function theCurrentCompanyPriceIs($value)
    {
        assertEquals($value, $this->resultOne->getValue());
    }

    /**
     * @Then /^I see "([^"]*)" company prices$/
     */
    public function iSeeCompanyPrices($counter)
    {
        assertContainsOnly(Price::class, $this->resultAll);
        assertCount((integer)$counter, $this->resultAll);
    }

    /**
     * @When /^I run script that pull price for "([^"]*)"$/
     */
    public function iRunScriptThatPullPriceFor($marketId)
    {
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);
        $this->pullPrice->pullPrice($company->getMarketId(), Carbon::yesterday());
    }

    /**
     * @When /^I run script that pull all prices$/
     */
    public function iRunScriptThatPullAllPrices()
    {
        $this->pullAllPrices->pullAllPrices(Carbon::yesterday());
    }

    /**
     * @Then I see :marketId company price downloaded for :relativeTime
     */
    public function iSeeCompanyPriceDownloadedFor($marketId, $relativeTime)
    {
        /** @var Company $company */
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        assertNotNull(
            $this->findPriceFor($company, new Carbon($relativeTime)),
            "No price for {$company->getMarketId()} for a $relativeTime"
        );
    }

    /**
     * @Then I see price value for :marketId for :relativeTime is not :value anymore
     */
    public function iSeePriceValueIsNotEqual($marketId, $relativeTime, $value)
    {
        /** @var Company $company */
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        assertNotEquals($value, $this->findPriceFor($company, new Carbon($relativeTime))->getValue());
    }

    /**
     * @Then /^I see all company prices downloaded for "([^"]*)"$/
     */
    public function iSeeAllCompanyPricesDownloadedFor($relativeTime)
    {
        /** @var Company[] $companies */
        $companies = $this->getCompanyRepository()->findAll();

        foreach ($companies as $company) {
            assertNotNull(
                $this->findPriceFor($company, new Carbon($relativeTime)),
                "No price for {$company->getMarketId()} for a day $relativeTime"
            );
        }
    }

    /**
     * @param DateTime $date
     *
     * @return array
     */
    private function findAllPricesFor(\DateTime $date)
    {
        return $this->getPriceRepository()->findBy(['identifier' => $date]);;
    }

    /**
     * @param Company $company
     * @param DateTime $date
     *
     * @return Price|null
     */
    private function findPriceFor(Company $company, \DateTime $date)
    {
        return $this->getPriceRepository()->findOneBy(['company' => $company, 'identifier' => $date]);;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getPriceRepository()
    {
        return $this->em->getRepository(Price::class);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getCompanyRepository()
    {
        return $this->em->getRepository(Company::class);
    }
}
