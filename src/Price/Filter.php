<?php

namespace Price;

use Price\Downloader\RawData;
use Price\Filter\FilteredData;

interface Filter
{
    /**
     * @param RawData $rawData
     * @param string $marketId
     *
     * @return FilteredData
     */
    public function filter(RawData $rawData, $marketId);
}