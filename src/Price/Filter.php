<?php

namespace Price;

use Price\Downloader\RawData;
use Price\Filter\FilteredData;
use Company\Entity\Company;

interface Filter
{
    /**
     * @param RawData $rawData
     * @param Company $company
     *
     * @return FilteredData
     */
    public function filter(RawData $rawData, $company);
}