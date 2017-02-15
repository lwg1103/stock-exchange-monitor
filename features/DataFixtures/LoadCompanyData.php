<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Company\Entity\Company;
use Company\Entity\Company\Type;

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

    private function addCompany($name, $id, $type)
    {
        $company = new Company($name, $id, $type);

        $this->manager->persist($company);
    }

    private function addWig30Companies()
    {
        $this->addCompany("Alior Bank", "ALR", Type::BANK);
        $this->addCompany("Asseco Poland", "ACP", Type::ORDINARY);
        $this->addCompany("Bank Millenium", "MIL", Type::BANK);
        $this->addCompany("Bogdanka", "LWB", Type::ORDINARY);
        $this->addCompany("BZ WBK", "BZW", Type::BANK);
        $this->addCompany("CCC", "CCC", Type::ORDINARY);
        $this->addCompany("CD Projejkt", "CDR", Type::ORDINARY);
        $this->addCompany("Cyfrowy Polsat", "CPS", Type::ORDINARY);
        $this->addCompany("ENEA", "ENA", Type::ORDINARY);
        $this->addCompany("Energa", "ENG", Type::ORDINARY);
        $this->addCompany("Eurocash", "EUR", Type::ORDINARY);
        $this->addCompany("Grupa Azoty", "ATT", Type::ORDINARY);
        $this->addCompany("Grupa Lotos", "LTS", Type::ORDINARY);
        $this->addCompany("GTC", "GTC", Type::ORDINARY);
        $this->addCompany("ING Bank Slaski", "ING", Type::BANK);
        $this->addCompany("Jastrzebska Spolka Weglowa", "JSW", Type::ORDINARY);
        $this->addCompany("Kernel Holding", "KER", Type::ORDINARY);
        $this->addCompany("KGHM Polska Miedz", "KGH", Type::ORDINARY);
        $this->addCompany("LPP", "LPP", Type::ORDINARY);
        $this->addCompany("MBANK", "MBK", Type::BANK);
        $this->addCompany("Orange Polska", "OPL", Type::ORDINARY);
        $this->addCompany("PEKAO", "PEO", Type::BANK);
        $this->addCompany("PGE", "PGE", Type::ORDINARY);
        $this->addCompany("PGNiG", "PGN", Type::ORDINARY);
        $this->addCompany("PKN Orlen", "PKN", Type::ORDINARY);
        $this->addCompany("PKO BP", "PKO", Type::BANK);
        $this->addCompany("PKP Cargo", "PKP", Type::ORDINARY);
        $this->addCompany("PZU", "PZU", Type::ORDINARY);
        $this->addCompany("Synthos", "SNS", Type::ORDINARY);
        $this->addCompany("Tauron Polska Energia", "TPE", Type::ORDINARY);
    }

    private function addWigBudowCompanies()
    {
        $this->addCompany("Elektrobudowa", "ELB", Type::ORDINARY);
    }

}