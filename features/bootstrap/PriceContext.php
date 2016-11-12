<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\Common\Persistence\ObjectManager;
use Application\UseCase\GetPrice;
use Application\UseCase\PullPrice;
use Application\UseCase\PullAllPrices;
use Price\Entity\Price;
use Company\Entity\Company;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class PriceContext implements Context, SnippetAcceptingContext
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
     * @Given /^There are no prices for "([^"]*)"$/
     */
    public function thereAreNoPricesFor($date)
    {
        $prices = $this->findAllPricesFor(new \DateTime($date));

        foreach ($prices as $price)
            $this->em->remove($price);

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
     * @Then /^The current company price is "([^"]*)" "([^"]*)"$/
     */
    public function theCurrentCompanyPriceIs($value, $currency)
    {
        assertEquals((integer)$value, $this->resultOne->getPrice()->getAmount());
        assertEquals($currency, $this->resultOne->getPrice()->getCurrency());
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
        $this->pullPrice->pullPrice($company->getMarketId());
    }

    /**
     * @When /^I run script that pull all prices$/
     */
    public function iRunScriptThatPullAllPrices()
    {
        $this->pullAllPrices->pullAllPrices();
    }

    /**
     * @Then /^I see "([^"]*)" company price downloaded for "([^"]*)"$/
     */
    public function iSeeCompanyPriceDownloadedFor($marketId, $date)
    {
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        assertNotNull(
            $this->findPriceFor($company, new \DateTime($date)),
            "No price for {$company->getMarketId()} for a day $date"
        );
    }

    /**
     * @Then /^I see all company prices downloaded for "([^"]*)"$/
     */
    public function iSeeAllCompanyPricesDownloadedFor($date)
    {
        /** @var Company[] $companies */
        $companies = $this->getCompanyRepository()->findAll();

        foreach ($companies as $company) {
            assertNotNull(
                $this->findPriceFor($company, new \DateTime($date)),
                "No price for {$company->getMarketId()} for a day $date"
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
