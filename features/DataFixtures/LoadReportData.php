<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Report;
use AppBundle\Entity\Report\Period as PeriodType;
use AppBundle\Entity\Report\Type as ReportType;

class LoadReportData implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        foreach ($this->getData() as $reportData) {
            $this->addReport($reportData);
        }

        $this->manager->flush();
    }

    private function getData()
    {
        return [
            [
                'type' => ReportType::AUTO,
                'period' => PeriodType::QUARTERLY,
                'identifier' => new \DateTime('31-03-2016'),
                'company' => $this->getCompany('PKO'),
                'income' => 500,
                'netProfit' => 20,
                'operationalNetProfit' => 18,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 200,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
            [
                'type' => ReportType::MANUAL,
                'identifier' => new \DateTime('31-12-2015'),
                'company' => $this->getCompany('PKO'),
                'income' => 400,
                'netProfit' => 20,
                'operationalNetProfit' => 18,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 200,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
            [
                'type' => ReportType::AUTO,
                'identifier' => new \DateTime('31-12-2015'),
                'company' => $this->getCompany('PKO'),
                'income' => 400,
                'netProfit' => 20,
                'operationalNetProfit' => 18,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 200,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
            [
                'identifier' => new \DateTime('31-12-2014'),
                'company' => $this->getCompany('PKO'),
                'income' => 300,
                'netProfit' => 20,
                'operationalNetProfit' => 19,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 200,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
            [
                'identifier' => new \DateTime('31-12-2013'),
                'company' => $this->getCompany('PKO'),
                'income' => 200,
                'netProfit' => 20,
                'operationalNetProfit' => 18,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 250,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
            [
                'identifier' => new \DateTime('31-12-2012'),
                'company' => $this->getCompany('PKO'),
                'income' => 100,
                'netProfit' => 20,
                'operationalNetProfit' => 18,
                'bookValue' => 450,
                'assets' => 330,
                'currentAssets' => 150,
                'liabilities' => 200,
                'currentLiabilities' => 100,
                'sharesQuantity' => 150000000,
            ],
        ];
    }

    private function getCompany($marketId)
    {
        return $this->manager->getRepository('AppBundle:Company')->find($marketId);
    }

    private function addReport(array $source)
    {
        $report = new Report();

        //with default value
        $report->setType(isset($source['type']) ? $source['type'] : ReportType::MANUAL)
            ->setPeriod(isset($source['period']) ? $source['period'] : PeriodType::ANNUALLY);

        //without defaults
        $report->setIdentifier($source['identifier'])
            ->setCompany($source['company'])
            ->setIncome($source['income'])
            ->setNetProfit($source['netProfit'])
            ->setOperationalNetProfit($source['operationalNetProfit'])
            ->setBookValue($source['bookValue'])
            ->setAssets($source['assets'])
            ->setCurrentAssets($source['currentAssets'])
            ->setLiabilities($source['liabilities'])
            ->setCurrentLiabilities($source['currentLiabilities'])
            ->setSharesQuantity($source['sharesQuantity']);

        $this->manager->persist($report);
    }

}