<?php

namespace AppBundle\UseCase;

use AppBundle\Entity\Company;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ListCompanies
 *
 * @package AppBundle\UseCase
 */
class ListCompanies
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * ListCompanies constructor.
     *
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Company[]
     */
    public function execute()
    {
        return $this->em->getRepository('AppBundle:Company')->findAll();
    }
}