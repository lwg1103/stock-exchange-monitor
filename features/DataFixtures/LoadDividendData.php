<?php

use Carbon\Carbon;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dividend\Entity\Dividend;


class LoadDividendData implements OrderedFixtureInterface, FixtureInterface
{
    const TIMEZONE = 'Europe/Warsaw';
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
            $this->addDividend($reportData);
        }

        $this->manager->flush();
    }

    private function getData()
    {
        return [
            [
                'period_from' => Carbon::createFromFormat("Y-m-d", '2015-01-01', self::TIMEZONE)->setTime(0,0,0),
                'period_to' => Carbon::createFromFormat("Y-m-d", '2015-12-31', self::TIMEZONE)->setTime(0,0,0),
                'company' => $this->getCompany('PKO'),
                'value' => 100,
                'currency' => 'PLN',
                'rate' => 3.74,
                'state' => 1,
                'payment_date' => null,
                'agm_date' => Carbon::createFromFormat("Y-m-d", '2016-06-30', self::TIMEZONE)->setTime(0,0,0)
            ],
            [
                'period_from' => Carbon::createFromFormat("Y-m-d", '2013-01-01', self::TIMEZONE)->setTime(0,0,0),
                'period_to' => Carbon::createFromFormat("Y-m-d", '2013-12-31', self::TIMEZONE)->setTime(0,0,0),
                'company' => $this->getCompany('PKO'),
                'value' => 75,
                'currency' => 'PLN',
                'rate' => 1.89,
                'state' => 3,
                'payment_date' => Carbon::createFromFormat("Y-m-d", '2014-10-03', self::TIMEZONE)->setTime(0,0,0),
                'agm_date' => Carbon::createFromFormat("Y-m-d", '2014-06-26', self::TIMEZONE)->setTime(0,0,0)
            ],
            [
                'period_from' => Carbon::createFromFormat("Y-m-d", '2012-01-01', self::TIMEZONE)->setTime(0,0,0),
                'period_to' => Carbon::createFromFormat("Y-m-d", '2012-12-31', self::TIMEZONE)->setTime(0,0,0),
                'company' => $this->getCompany('PKO'),
                'value' => 180,
                'currency' => 'PLN',
                'rate' => 4.73,
                'state' => 3,
                'payment_date' => Carbon::createFromFormat("Y-m-d", '2013-10-04', self::TIMEZONE)->setTime(0,0,0),
                'agm_date' => Carbon::createFromFormat("Y-m-d", '2013-06-20', self::TIMEZONE)->setTime(0,0,0)
            ],
            [
                'period_from' => Carbon::createFromFormat("Y-m-d", '2011-01-01', self::TIMEZONE)->setTime(0,0,0),
                'period_to' => Carbon::createFromFormat("Y-m-d", '2011-12-31', self::TIMEZONE)->setTime(0,0,0),
                'company' => $this->getCompany('PKO'),
                'value' => 127,
                'currency' => 'PLN',
                'rate' => 3.99,
                'state' => 3,
                'payment_date' => Carbon::createFromFormat("Y-m-d", '2012-06-27', self::TIMEZONE)->setTime(0,0,0),
                'agm_date' => Carbon::createFromFormat("Y-m-d", '2012-06-06', self::TIMEZONE)->setTime(0,0,0)
            ]
        ];
    }

    private function getCompany($marketId)
    {
        return $this->manager->getRepository('CompanyContext:Company')->find($marketId);
    }

    private function addDividend(array $source)
    {
        $dividend = new Dividend();

        //without defaults
        $dividend->setPeriodFrom($source['period_from'])
            ->setPeriodTo($source['period_to'])
            ->setCompany($source['company'])
            ->setValue($source['value'])
            ->setCurrency($source['currency'])
            ->setRate($source['rate'])
            ->setState($source['state'])
            ->setPaymentDate($source['payment_date'])
            ->setAgmDate($source['agm_date']);

        $this->manager->persist($dividend);
    }

}