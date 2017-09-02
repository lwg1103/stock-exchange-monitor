<?php

use Behat\Behat\Context\Context;
use Application\UseCase\GetReport;
use Application\UseCase\AddReport;
use Symfony\Component\Form\FormFactory;
use Report\Entity\Report;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Defines application features from the specific context.
 */
class ReportContext implements Context
{
    /** @var Report */
    protected $result;
    protected $currentCount;
    /**
     * @var ObjectManager
     */
    protected $em;
    /**
     * @var GetReport
     */
    private $getReport;
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
     * @param GetReport     $getReport,
     * @param AddReport     $addReportManually
     * @param FormFactory   $formFactory
     * @param ObjectManager $em
     */
    public function __construct(
        GetReport       $getReport,
        AddReport       $addReportManually,
        FormFactory     $formFactory,
        ObjectManager   $em
    )
    {
        $this->getReport            = $getReport;
        $this->addReportManually    = $addReportManually;
        $this->formFactory          = $formFactory;
        $this->em                   = $em;
    }

    /**
     * @When I check reports for :marketId company
     */
    public function iCheckReportsForCompany($marketId)
    {
        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
        $this->result = $this->getReport->allByCompany($company);
        $this->currentCount = count($this->result);
    }

    /**
     * @When I check :period report for :company with identifier :date
     */
    public function iCheckReportForWithIdentifier($period, $marketId, $date)
    {
        $reportPeriod = $this->getPeriodFromString($period);

        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
        $this->result = $this->getReport->oneByIdentifier($company, new \DateTime($date), $reportPeriod);
    }

    /**
     * @When I add :identifier report manually for :marketId company
     */
    public function iAddReportManually($identifier, $marketId)
    {
        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
        
        $report = new Report();

        $report->setCompany($company)
            ->setIdentifier(new \DateTime($identifier))
            ->setType(Report\Type::MANUAL)
            ->setPeriod(Report\Period::ANNUAL)
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
     * @Then I see one additional report for :marketId company
     */
    public function iSeeOneAdditionalReportForCompany($marketId)
    {
        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
        $this->result = $this->getReport->allByCompany($company);

        assertCount($this->currentCount+1, $this->result);
    }

    /**
     * @Then I see all reports for :marketId company
     */
    public function iSeeAllReportsForCompany($marketId)
    {
        $company = $this->em->getRepository('CompanyContext:Company')->findOneBy(['marketId' => $marketId]);
        $expectedResult = $this->em->getRepository('ReportContext:Report')->findBy(['company' => $company]);

        assertCount(count($expectedResult), $this->result);
        assertContainsOnly('Report\Entity\Report', $this->result);
        assertEquals($expectedResult, $this->result);
    }

    /**
     * @Then I should see :parameterName at :value in the result report
     */
    public function iShouldSeeParameterAtValueInTheResultReport($parameterName, $value)
    {
        $methodName = "get" . $parameterName;

        assertEquals($value, $this->result->$methodName());
    }

    /**
     * @Then I should see :type report type
     */
    public function iShouldSeeReportType($type)
    {
        $reportType = $this->getTypeFromString($type);

        assertEquals($reportType, $this->result->getType());
    }

    /**
     * @param $period
     * @return int
     * @throws Exception
     */
    private function getPeriodFromString($period)
    {
        switch ($period) {
            case 'annual':
                return Report\Period::ANNUAL;
            case 'biannual':
                return Report\Period::BIANNUAL;
            case 'quarterly':
                return Report\Period::QUARTERLY;
            default:
                throw new \Exception("Wrong report period");
        }
    }

    /**
     * @param $type
     * @return int
     * @throws Exception
     */
    private function getTypeFromString($type)
    {
        switch ($type) {
            case 'auto':
                return Report\Type::AUTO;
            case 'manual':
                return Report\Type::MANUAL;
            default:
                throw new \Exception("Wrong report type");
        }
    }
}
