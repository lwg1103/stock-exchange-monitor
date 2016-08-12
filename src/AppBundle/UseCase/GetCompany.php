<?php

namespace AppBundle\UseCase;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Company;

/**
 * Class GetCompany
 * @package AppBundle\UseCase
 */
class GetCompany
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
     * @param string $marketId
     *
     * @return Company
     */
    public function byMarketId($marketId)
    {
        return $this->entityRepository->findOneBy(['marketId' => $marketId]);
    }
}