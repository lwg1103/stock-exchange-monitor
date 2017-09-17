<?php

namespace Report\Loader;

use Report\Entity\Report;
use Doctrine\ORM\EntityManager;

/**
 * Class Loader
 * 
 * @package AppBundle\Loader
 */
class ParserReportLoader
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

    public function loadReportIfNeeded(Report $report) {
        if($this->needStoreReport($report)) {
            $this->load($report);
        }
    }
    
    public function needStoreReport(Report $report)
    {
        $storedReport = $this->em->getRepository('ReportContext:Report')->findOneBy([
            'company' => $report->getCompany(),
            'identifier' => $report->getIdentifier(),
            'period' => $report->getPeriod(),
            'type' => Report\Type::AUTO
        ]);
    
        return ! (null != $storedReport);
    }
    
    /**
     * @param Report $report
     */
    private function load(Report $report)
    {
        $this->em->persist($report);
        $this->em->flush();
    }
}