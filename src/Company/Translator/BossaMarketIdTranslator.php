<?php

namespace Company\Translator;

class BossaMarketIdTranslator
{
    private $bossaMarketId = [
        'ALR' => "ALIOR",
        'ACP' => "ASSECOPOL",
        'MIL' => "MILLENNIUM",
        'LWB' => "BOGDANKA",
        'BZW' => "BZWBK",
        'CCC' => "CCC",
        'CDR' => "CDPROJEKT",
        'CPS' => "CYFRPLSAT",
        'ENA' => "ENEA",
        'ENG' => "ENERGA",
        'EUR' => "EUROCASH",
        'ATT' => "GRUPAAZOTY",
        'LTS' => "LOTOS",
        'GTC' => "GTC",
        'ING' => "INGBSK",
        'JSW' => "JSW",
        'KER' => "KERNEL",
        'KGH' => "KGHM",
        'LPP' => "LPP",
        'MBK' => "MBANK",
        'OPL' => "ORANGEPL",
        'PEO' => "PEKAO",
        'PGE' => "PGE",
        'PGN' => "PGNIG",
        'PKN' => "PKNORLEN",
        'PKO' => "PKOBP",
        'PKP' => "PKPCARGO",
        'PZU' => "PZU",
        'SNS' => "SYNTHOS",
        'TPE' => "TAURONPE",
        'ELB' => "ELBUDOWA",
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