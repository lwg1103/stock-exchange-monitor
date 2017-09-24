<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityRepository;
use Price\Entity\Price;
use Company\Entity\Company;

/**
 * Class GetDividend
 * 
 * @package AppBundle\UseCase
 */
class GetDividend
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
        return $this->entityRepository->findOneBy(['company' => $company], ['periodFrom' => 'desc']);
    }

    /**
     * @param Company $company
     *
     * @return Price[]
     */
    public function allByCompany(Company $company)
    {
        //latest are most interesting
        return $this->entityRepository->findBy(['company' => $company], ['periodFrom' => 'desc']);
    }
}