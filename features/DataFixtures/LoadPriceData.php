<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Price\Entity\Price;
use Money\Money;
use Carbon\Carbon;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPriceData implements OrderedFixtureInterface, FixtureInterface
{
    private $manager;

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        Carbon::setTestNow(\Carbon\Carbon::create(2016,9,10));
        $this->addPrice("PKO", new Money(123, new \Money\Currency('PLN')));

        Carbon::setTestNow(\Carbon\Carbon::create(2016,9,9));
        $this->addPrice("PKO", new Money(122, new \Money\Currency('PLN')));

        Carbon::setTestNow(\Carbon\Carbon::create(2016,9,8));
        $this->addPrice("PKO", new Money(121, new \Money\Currency('PLN')));

        $this->manager->flush();
    }

    private function addPrice($companyMarketId, $priceValue)
    {
        $price = new Price($this->getCompany($companyMarketId), $priceValue);

        $this->manager->persist($price);
    }

    private function getCompany($marketId)
    {
        return $this->manager->getRepository('CompanyContext:Company')->find($marketId);
    }
}