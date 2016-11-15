<?php

namespace Company\Translator;

class BossaMarketIdTranslator
{
    private $bossaMarketId = [
        'ACP' => "ASSECOPOL",
        'ELB' => "ELBUDOWA",
        'PGN' => "PGNIG",
        'PKO' => "PKOBP"
    ];

    public function translateFromMarketId($marketId)
    {
        if (!array_key_exists($marketId, $this->bossaMarketId)) {
            throw new TranslatorException(sprintf("Missing BossaId for %s", $marketId));
        }

        return $this->bossaMarketId[$marketId];
    }
    
    public function translateToMarketId($bossaMarketId)
    {
        $key = array_search($bossaMarketId, $this->bossaMarketId);

        if (!$key) {
            throw new TranslatorException(sprintf("Missing MarketId for %s", $bossaMarketId));
        }

        return $key;
    }
}