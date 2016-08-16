<?php

use Behat\Behat\Context\Context;
use AppBundle\UseCase\ListReports;
use AppBundle\UseCase\AddReport;
use Symfony\Component\Form\FormFactory;
use AppBundle\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class ReportContext implements Context
{
    protected $result;
    protected $currentCount;
    /**
     * @var ObjectManager
     */
    protected $em;
    /**
     * @var ListReports
     */
    private $listReports;
    /**
     * @var AddReport
     */
    private $addReportManually;
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * ReportContext constructor.
     *
     * @param ListReports   $listReports,
     * @param AddReport     $addReportManually
     * @param FormFactory   $formFactory
     * @param ObjectManager $em
     */
    public function __construct(
        ListReports     $listReports,
        AddReport       $addReportManually,
        FormFactory     $formFactory,
        ObjectManager   $em
    )
    {
        $this->listReports          = $listReports;
        $this->addReportManually    = $addReportManually;
        $this->formFactory          = $formFactory;
        $this->em                   = $em;
    }

    /**
     * @When I check reports for :arg1 company
     */
    public function iCheckReportsForCompany($arg1)
    {
        $company = $this->em->getRepository('AppBundle:Company')->findOneBy(['marketId' => $arg1]);
        $this->result = $this->listReports->byCompany($company);
        $this->currentCount = count($this->result);
    }

    /**
     * @When I add report manually for :arg1 company
     */
    public function iAddReportManually($arg1)
    {
        $company = $this->em->getRepository('AppBundle:Company')->findOneBy(['marketId' => $arg1]);
        
        $report = new Report();

        $report->setCompany($company)
            ->setIdentifier(new \DateTime('31-12-2010'))
            ->setType(Report\Type::MANUAL)
            ->setPeriod(Report\Period::ANNUALLY)
            ->setAssets(1)
            ->setBookValue(2)
            ->setCurrentAssets(3)
            ->setCurrentLiabilities(4)
            ->setIncome(5)
            ->setLiabilities(6)
            ->setNetProfit(7)
            ->setOperationalNetProfit(8)
            ->setSharesQuantity(9);

        $form = $this->formFactory->create(\AppBundle\Form\ReportType::class, $report);
        
        $this->addReportManually->add($form);
    }

    /**
     * @Then I see one additional report for :arg1 company
     */
    public function iSeeOneAdditionalReportForCompany($arg1)
    {
        $company = $this->em->getRepository('AppBundle:Company')->findOneBy(['marketId' => $arg1]);
        $this->result = $this->listReports->byCompany($company);

        assertCount($this->currentCount+1, $this->result);
    }

    /**
     * @Then /^I see all reports for "([^"]*)" company$/
     */
    public function iSeeAllReportsForCompany($arg1)
    {
        $company = $this->em->getRepository('AppBundle:Company')->findOneBy(['marketId' => $arg1]);
        $expectedResult = $this->em->getRepository('AppBundle:Report')->findBy(['company' => $company]);

        assertCount(count($expectedResult), $this->result);
        assertContainsOnly('AppBundle\Entity\Report', $this->result);
        assertEquals($expectedResult, $this->result);
    }
}
