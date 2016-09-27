<?php

namespace Application\UseCase;

use Company\Entity\Company;
use Report\Entity\Report;
use Doctrine\ORM\EntityRepository;

/**
 * Class ListReports
 * 
 * @package AppBundle\UseCase
 */
class ListReports
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * ListReports constructor.
     * 
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Company $company
     * 
     * @return Report[]
     */
    public function byCompany(Company $company)
    {
        return $this->repository->findBy(['company' => $company]);
    }
}