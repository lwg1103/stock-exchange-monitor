<?php

namespace Price\Parser;

use Carbon\Carbon;
use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Price\Entity\Price;
use Price\Filter\FilteredData;
use Price\Parser;

class BossaParser implements Parser
{
    /** @var EntityRepository */
    private $companyRepository;

    /**
     * BossaParser constructor.
     * @param EntityRepository $companyRepository
     */
    public function __construct(EntityRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(FilteredData $filteredData)
    {
        return new Price(
            $this->findCompanyForId($this->getMarketId($filteredData->value)), 
            $this->getPrice($filteredData->value),
            $this->getDate($filteredData->value)
        );
    }

    /**
     * @param $marketId
     * 
     * @return Company
     */
    private function findCompanyForId($marketId)
    {
        return $this->companyRepository->findOneBy(['marketId' => $marketId]);
    }

    private function getMarketId($filteredString)
    {
        $bossaMarketId = explode(",", $filteredString)[0];

        return $this->companyRepository->findOneBy(['longMarketId' => $bossaMarketId]);
    }

    /**
     * @param $filteredString
     *
     * @return int
     */
    private function getPrice($filteredString)
    {
        $price = explode(",", $filteredString)[5];

        return (float)$price;
    }

    private function getDate($filteredString)
    {
        $date =  explode(",", $filteredString)[1];

        return Carbon::createFromFormat("Ymd", $date, 'Europe/Warsaw')->setTime(0,0,0);
    }
}