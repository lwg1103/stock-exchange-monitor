<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Company;

class LoadCompanyData implements OrderedFixtureInterface, FixtureInterface
{
    private $manager;

    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->addCompany("PKO BP", "PKO");
        $this->addCompany("PGNiG", "PGN");
        $this->addCompany("Elektrobudowa", "ELB");
        $this->addCompany("Asseco Poland", "ACP");

        $this->manager->flush();
    }

    private function addCompany($name, $id)
    {
        $company = new Company($name, $id);

        $this->manager->persist($company);
    }

}