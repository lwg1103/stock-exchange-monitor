<?php

namespace Price\Filter;

use Price\Downloader\RawData;
use Price\Filter;

class BossaFilter implements Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(RawData $rawData, $company)
    {
        return $this->findCompanyRow($rawData, $company->getLongMarketId());
    }
    
    private function findCompanyRow(RawData $rawData, $bossaId)
    {
        $result = preg_grep("/^" . $bossaId . "[,].*/", $rawData->rows);
        
        if (count($result) != 1) {
            throw new FilterException(sprintf("One result for %s expected, %d received", $bossaId, count($result)));
        }

        return new FilteredData(reset($result));
    }
}