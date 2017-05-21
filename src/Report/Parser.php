<?php
namespace Report;

use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;
use Report\Reader\ReportReader;
use Report\Loader\ReportLoader;
use Monolog\Logger;

abstract class Parser implements ParserInterface
{

    /**
     *
     * @var Company
     */
    protected $company;

    /**
     *
     * @var EntityRepository
     */
    protected $er;

    /**
     *
     * @var ReportLoader
     */
    protected $loader;

    /**
     *
     * @var ReportReader
     */
    protected $reader;

    /**
     *
     * @var string
     */
    protected $html;

    /**
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Loader constructor.
     * 
     * @param ReportReader $reader
     * @param Logger $logger
     * 
     */
    public function __construct(EntityRepository $er, ReportReader $reader, Logger $logger)
    {
        $this->er = $er;
        $this->reader = $reader;
        $this->logger = $logger;
    }

    /**
     *
     * @param Company $company
     * @return Report[]
     */
    abstract public function parse(Company $company);

    protected function saveReports($reports)
    {
        $this->log('[S] saving reports');
        foreach ($reports as $report) {
            $objReport = $this->reader->read($report);
            $this->loader->loadReportINeeded($objReport);
        }
        $this->log('[E] saving reports');
    }

    protected function log($message)
    {
        $this->logger->info($message);
        //echo $message . '\n';
    }
}