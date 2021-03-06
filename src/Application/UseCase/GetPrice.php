<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityRepository;
use Price\Entity\Price;
use Company\Entity\Company;

/**
 * Class GetPrice
 * 
 * @package AppBundle\UseCase
 */
class GetPrice
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @param EntityRepository $entityRepository
     */
    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @param Company $company
     *
     * @return Price
     */
    public function lastByCompany(Company $company)
    {
        $price = $this->entityRepository->findOneBy(['company' => $company], ['identifier' => 'desc']);
        if(!$price) {
            $price = new Price($company, Price::NO_DATA_VALUE);
        }
        return $price;
    }

    /**
     * @param Company $company
     *
     * @return Price[]
     */
    public function allByCompany(Company $company)
    {
        return $this->entityRepository->findBy(['company' => $company]);
    }
}