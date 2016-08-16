<?php

namespace AppBundle\UseCase;

use AppBundle\Loader\ReportLoader;
use AppBundle\Reader\ReportReader;

/**
 * Class AddReport
 * 
 * @package AppBundle\UseCase
 */
class AddReport
{
    /**
     * @var ReportReader
     */
    protected $reader;
    /**
     * @var ReportLoader
     */
    protected $loader;

    /**
     * AddReport constructor.
     * @param ReportReader $reader
     * @param ReportLoader $loader
     */
    public function __construct(ReportReader $reader, ReportLoader $loader)
    {
        $this->reader = $reader;
        $this->loader = $loader;
    }

    /**
     * @param mixed $input
     */
    public function add($input)
    {
        $this->loader->load($this->reader->read($input));
    }
}
