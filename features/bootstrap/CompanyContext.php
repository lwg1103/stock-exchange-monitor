<?php

use Behat\Behat\Context\Context;
use AppBundle\UseCase\ListCompanies;
use AppBundle\UseCase\GetCompany;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Defines application features from the specific context.
 */
class CompanyContext implements Context
{
    protected $result;
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var ListCompanies
     */
    private $listCompanies;
    /**
     * @var GetCompany
     */
    private $getCompany;

    /**
     * CompanyContext constructor.
     *
     * @param ListCompanies $listCompanies
     * @param GetCompany    $getCompany
     * @param ObjectManager $em
     */
    public function __construct(
        ListCompanies   $listCompanies,
        GetCompany      $getCompany,
        ObjectManager   $em
    )
    {
        $this->listCompanies    = $listCompanies;
        $this->getCompany       = $getCompany;
        $this->em               = $em;
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


    /**
     * @When I enter :arg1 company site
     */
    public function iEnterCompanySite($arg1)
    {
        $this->result = $this->getCompany->byMarketId($arg1);
    }

    /**
     * @Then I get :arg1 company details
     */
    public function iGetCompanyDetails($arg1)
    {
        $expected = $this->em->getRepository('AppBundle:Company')->findOneBy(['marketId' => $arg1]);

        assertEquals($expected, $this->result);
    }
}
