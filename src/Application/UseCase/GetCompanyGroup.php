<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityRepository;
use Company\Entity\Company\Group;

/**
 * Class GetCompanyGroup
 * 
 * @package AppBundle\UseCase
 */
class GetCompanyGroup
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
     * @param int $id
     *
     * @return Group
     */
    public function byId($id)
    {
        return $this->entityRepository->find($id);
    }
    
    /**
     * @param string $name
     *
     * @return Group
     */
    public function byName($name)
    {
        return $this->entityRepository->findOneBy(['name' => $name]);
    }
}