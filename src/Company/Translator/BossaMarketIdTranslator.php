<?php

namespace Company\Translator;

use Doctrine\ORM\EntityRepository;

class BossaMarketIdTranslator
{
    /** @var EntityRepository */
    private $companyRepository;
    
    /**
     * BossaMarketIdTranslator constructor.
     * @param EntityRepository $companyRepository
     */
    public function __construct(EntityRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }
    

    public function translateFromMarketId($marketId)
    {
        $company = $this->companyRepository->findOneBy(['marketId' => $marketId]);
        if(!$company || !$company->getLongMarketId()) {
            throw new TranslatorException(sprintf("Missing BossaId for %s", $marketId));
        }
        
        return $company->getLongMarketId();
    }
    
    public function translateToMarketId($bossaMarketId)
    {
        $company = $this->companyRepository->findOneBy(['longMarketId' => $bossaMarketId]);
        if(!$company) {
            throw new TranslatorException(sprintf("Missing company for bossaId (long_market_id) %s", $bossaMarketId));
        }
        
        return $company->getMarketId();
    }
}