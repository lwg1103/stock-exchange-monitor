<?php

namespace Price\Filter;

use Company\Translator\BossaMarketIdTranslator;
use Price\Downloader\RawData;
use Price\Filter;

class BossaFilter implements Filter
{
    /** @var BossaMarketIdTranslator */
    private $bossaTranslator;

    /**
     * BossaFilter constructor.
     * @param BossaMarketIdTranslator $bossaTranslator
     */
    public function __construct(BossaMarketIdTranslator $bossaTranslator)
    {
        $this->bossaTranslator = $bossaTranslator;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(RawData $rawData, $marketId)
    {
        return $this->findCompanyRow($rawData, $this->bossaTranslator->translateFromMarketId($marketId));
    }
    
    private function findCompanyRow(RawData $rawData, $bossaId)
    {
        $result = preg_grep("/" . $bossaId . "*/", $rawData->rows);
        
        if (count($result) != 1) {
            throw new FilterException(sprintf("One result for %s expected, %d received", $bossaId, count($result)));
        }

        return new FilteredData(reset($result));
    }
}