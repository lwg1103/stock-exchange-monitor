<?php

namespace Application\UseCase;

use Dividend\Entity\Dividend;
use Doctrine\ORM\EntityRepository;
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
     * @return Dividend
     */
    public function lastByCompany(Company $company)
    {
        return $this->entityRepository->findOneBy(['company' => $company], ['periodFrom' => 'desc']);
    }

    /**
     * @param Company $company
     * @param string $sort
     *
     * @return Dividend[]
     */
    public function allByCompany(Company $company, $sort = 'desc')
    {
        return $this->entityRepository->findBy(['company' => $company], ['periodFrom' => $sort]);
    }
}