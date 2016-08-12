<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Company;
use Doctrine\ORM\EntityRepository;

/**
 * Class ListCompanies
 *
 * @package AppBundle\UseCase
 */
class ListCompanies
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * ListCompanies constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
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