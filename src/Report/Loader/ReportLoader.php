<?php

namespace Report\Loader;

use Report\Entity\Report;
use Doctrine\ORM\EntityManager;

/**
 * Class Loader
 * 
 * @package AppBundle\Loader
 */
class ReportLoader
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Loader constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Report $report
     */
    public function load(Report $report)
    {
        $this->em->persist($report);
        $this->em->flush();
    }
}