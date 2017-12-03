<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Application\UseCase\GetDividend;
use Dividend\Entity\Dividend;
use Company\Entity\Company;
use Carbon\Carbon;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class DividendContext implements Context
{
    /** @var  Dividend[] */
    protected $resultAll;
    /** @var ObjectManager */
    protected $em;
    /** @var GetDividend */
    private $getDividend;

    /**
     * CompanyContext constructor.
     *
     * @param GetDividend       $getDividend
     * @param ObjectManager     $em
     */
    public function __construct(
        GetDividend     $getDividend,
        ObjectManager   $em
    )
    {
        $this->getDividend  = $getDividend;
        $this->em           = $em;
    }

    /**
     * @When /^I enter "([^"]*)" company dividend site$/
     */
    public function iEnterCompanyDividendSite($marketId)
    {
        $company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]);

        $this->resultAll = $this->getDividend->allByCompany($company);
    }

    /**
     * @When :company company has dividend for period from :from to :to with a rate :rate
     */
    public function companyHasDividendForPeriodFromToWithARate($marketId, $from, $to, $rate)
    {
        $dividend = new Dividend();
        
        $dividend->setCompany($company = $this->getCompanyRepository()->findOneBy(['marketId' => $marketId]))
            ->setPeriodFrom(Carbon::createFromFormat("d-m-Y", $from))
            ->setPeriodTo(Carbon::createFromFormat("d-m-Y", $to))
            ->setRate($rate)
            ->setValue(1)
            ->setCurrency('PLN')
            ->setState(3)
            ->setAgmDate(Carbon::createFromFormat("d-m-Y", $to))
            ->setPaymentDate(Carbon::createFromFormat("d-m-Y", $to));

        $this->em->persist($dividend);
        $this->em->flush();
    }

    /**
     * @Then /^I see at least "([^"]*)" company dividends$/
     */
    public function iSeeAtLeastCompanyDividends($counter)
    {
        assertContainsOnly(Dividend::class, $this->resultAll);
        assertLessThanOrEqual(count($this->resultAll), (int)$counter);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function getCompanyRepository()
    {
        return $this->em->getRepository(Company::class);
    }
}
