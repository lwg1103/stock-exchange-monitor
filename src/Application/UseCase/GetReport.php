<?php

namespace Application\UseCase;

use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;

class GetReport
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
     * @return Report
     */
    public function byMarketId($marketId)
    {
        return $this->entityRepository->findOneBy(['marketId' => $marketId]);
    }
}