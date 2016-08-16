<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Company;
use AppBundle\Repository\CompanyRepository;

/**
 * Class ListCompanies
 *
 * @package AppBundle\UseCase
 */
class ListCompanies
{
    /**
     * @var CompanyRepository
     */
    private $repository;

    /**
     * ListCompanies constructor.
     *
     * @param CompanyRepository $repository
     */
    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Company[]
     */
    public function execute()
    {
        return $this->repository->findAll();
    }
}