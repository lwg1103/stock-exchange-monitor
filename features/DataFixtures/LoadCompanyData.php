<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Company\Entity\Company;

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

        $this->addWig30Companies();
        $this->addWigBudowCompanies();

        $this->manager->flush();
    }

    private function addCompany($name, $id)
    {
        $company = new Company($name, $id);

        $this->manager->persist($company);
    }

    private function addWig30Companies()
    {
        $this->addCompany("Alior Bank", "ALR");
        $this->addCompany("Asseco Poland", "ACP");
        $this->addCompany("Bank Millenium", "MIL");
        $this->addCompany("Bogdanka", "LWB");
        $this->addCompany("BZ WBK", "BZW");
        $this->addCompany("CCC", "CCC");
        $this->addCompany("CD Projejkt", "CDR");
        $this->addCompany("Cyfrowy Polsat", "CPS");
        $this->addCompany("ENEA", "ENA");
        $this->addCompany("Energa", "ENG");
        $this->addCompany("Eurocash", "EUR");
        $this->addCompany("Grupa Azoty", "ATT");
        $this->addCompany("Grupa Lotos", "LTS");
        $this->addCompany("GTC", "GTC");
        $this->addCompany("ING Bank Slaski", "ING");
        $this->addCompany("Jastrzebska Spolka Weglowa", "JSW");
        $this->addCompany("Kernel Holding", "KER");
        $this->addCompany("KGHM Polska Miedz", "KGH");
        $this->addCompany("LPP", "LPP");
        $this->addCompany("MBANK", "MBK");
        $this->addCompany("Orange Polska", "OPL");
        $this->addCompany("PEKAO", "PEO");
        $this->addCompany("PGE", "PGE");
        $this->addCompany("PGNiG", "PGN");
        $this->addCompany("PKN Orlen", "PKN");
        $this->addCompany("PKO BP", "PKO");
        $this->addCompany("PKP Cargo", "PKP");
        $this->addCompany("PZU", "PZU");
        $this->addCompany("Synthos", "SNS");
        $this->addCompany("Tauron Polska Energia", "TPE");
    }

    private function addWigBudowCompanies()
    {
        $this->addCompany("Elektrobudowa", "ELB");
    }

}