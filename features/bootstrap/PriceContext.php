<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\Common\Persistence\ObjectManager;
use Application\UseCase\GetPrice;
use Price\Entity\Price;

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
    /**
     * @var ObjectManager
     */
    protected $em;
    /**
     * @var GetPrice
     */
    private $getPrice;

    /**
     * CompanyContext constructor.
     *
     * @param GetPrice    $getPrice
     * @param ObjectManager $em
     */
    public function __construct(
        GetPrice        $getPrice,
        ObjectManager   $em
    )
    {
        $this->getPrice = $getPrice;
        $this->em       = $em;
    }

    /**
     * @When /^I enter "([^"]*)" company price site$/
     */
    public function iEnterCompanyPriceSite($arg1)
    {
        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $arg1]);

        $this->resultAll = $this->getPrice->allByCompany($company);
        $this->resultOne = $this->getPrice->lastByCompany($company);
    }

    /**
     * @Then /^I see "([^"]*)" current company price$/
     */
    public function iSeeLastCompanyPrice($arg1)
    {        
        assertInstanceOf(Price::class, $this->resultOne);
    }

    /**
     * @Given /^The current "([^"]*)" company price is "([^"]*)" "([^"]*)"$/
     */
    public function theCurrentCompanyPriceIs($arg1, $arg2, $arg3)
    {
        assertEquals((integer)$arg2, $this->resultOne->getPrice()->getAmount());
        assertEquals($arg3, $this->resultOne->getPrice()->getCurrency());
    }

    /**
     * @Then /^I see "([^"]*)" "([^"]*)" company prices$/
     */
    public function iSeeCompanyPrices($arg1, $arg2)
    {
        assertContainsOnly(Price::class, $this->resultAll);
        assertCount((integer)$arg2, $this->resultAll);
    }
}
