<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityRepository;
use Company\Entity\Company;

class PullAllPrices
{
    /** @var PullPrice */
    private $pullPrice;
    /** @var EntityRepository */
    private $companyRepository;

    /**
     * PullAllPrices constructor.
     * @param PullPrice $pullPrice
     * @param EntityRepository $companyRepository
     */
    public function __construct(PullPrice $pullPrice, EntityRepository $companyRepository)
    {
        $this->pullPrice = $pullPrice;
        $this->companyRepository = $companyRepository;
    }

    public function pullAllPrices()
    {
        /** @var Company[] $companies */
        $companies = $this->companyRepository->findAll();

        foreach ($companies as $company) {
            $this->pullPrice->pullPrice($company->getMarketId());
        }
    }
}