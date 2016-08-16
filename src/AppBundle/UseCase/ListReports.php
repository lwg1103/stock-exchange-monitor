<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Company;
use AppBundle\Entity\Report;
use AppBundle\Repository\ReportRepository;

/**
 * Class ListReports
 * 
 * @package AppBundle\UseCase
 */
class ListReports
{
    /**
     * @var ReportRepository
     */
    private $repository;

    /**
     * ListReports constructor.
     * 
     * @param ReportRepository $repository
     */
    public function __construct(ReportRepository $repository)
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