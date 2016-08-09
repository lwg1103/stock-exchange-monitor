<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class CompanyContext extends FeatureContext
{
    /**
     * @var \AppBundle\UseCase\ListCompanies
     */
    private $listCompanies;
    private $result;

    /**
     * CompanyContext constructor.
     * @param \AppBundle\UseCase\ListCompanies $listCompanies
     */
    public function __construct(\AppBundle\UseCase\ListCompanies $listCompanies)
    {
        $this->listCompanies = $listCompanies;
    }

    /**
     * @When I list companies
     */
    public function iListCompanies()
    {
        $this->result = $this->listCompanies->execute();
    }

    /**
     * @Then I see all companies in the system
     */
    public function iSeeAllCompaniesInTheSystem()
    {
        assertEquals(4, count($this->result));
        assertContainsOnly('AppBundle\Entity\Company', $this->result);
    }

}
