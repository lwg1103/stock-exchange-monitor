<?php

namespace Dividend\Loader;

use Dividend\Entity\Dividend;
use Doctrine\ORM\EntityManager;

/**
 * Class DividendLoader
 *
 * @package Dividend\Loader
 */
class DividendLoader
{
    /**
     * @var EntityManager
     */
    protected $em;

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
    public function load(Dividend $dividend)
    {
        $this->em->persist($dividend);
        $this->em->flush();
    }
}