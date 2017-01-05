<?php

namespace Price;

use Price\Downloader\NoFileException;
use Price\Downloader\RawData;

interface Downloader
{
    /**
     * @param \DateTime $date
     * 
     * @throws NoFileException
     * 
     * @return RawData
     */
    public function download(\DateTime $date);
}