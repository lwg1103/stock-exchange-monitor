<?php

namespace Application\UseCase;

use Company\Entity\Company\Group;
use Doctrine\ORM\EntityRepository;

/**
 * Class ListCompanyGroups
 *
 * @package AppBundle\UseCase
 */
class ListCompanyGroups
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * ListCompanyGroups constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Group[]
     */
    public function execute()
    {
        return $this->repository->findAll();
    }
}