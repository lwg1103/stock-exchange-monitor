<?php

namespace Application\UseCase;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Price\Entity\Price;
use Price\Downloader;
use Price\Filter;
use Price\Parser;

/**
 * Class GetPrice
 * 
 * @package AppBundle\UseCase
 */
class PullPrice
{
    /** @var EntityManager */
    private $entityManager;
    /** @var Downloader */
    private $downloader;
    /** @var Filter */
    private $filter;
    /** @var Parser */
    private $parser;

    /**
     * PullPrices constructor.
     * @param EntityManager $entityManager
     * @param Downloader $downloader
     * @param Filter $filter
     * @param Parser $parser
     */
    public function __construct(
        EntityManager       $entityManager, 
        Downloader          $downloader,
        Filter              $filter,
        Parser              $parser
    )
    {
        $this->entityManager        = $entityManager;
        $this->downloader           = $downloader;
        $this->filter               = $filter;
        $this->parser               = $parser;
    }

    public function pullPrice($company, $date)
    {
        try {
            $rawData = $this->downloader->download($date);
            $filteredData = $this->filter->filter($rawData, $company);
            $price = $this->parser->parse($filteredData);

            $this->storePrices($price);
        } catch (Downloader\NoFileException $e) {

        }
    }
    
    public function pullPriceByMarketId($marketId, $date) {
        $company = $this->entityManager->getRepository('ComapnyContext:Company')->findOneBy([
            'marketId' => $marketId
        ]);
        
        $this->pullPrice($company, $date);
    }

    /**
     * @param Price $price
     */
    private function storePrices($price)
    {
        $storedPrice = $this->getStoredPrice($price->getCompany(), $price->getIdentifier());

        if ($storedPrice) {
            $this->updatePrice($storedPrice, $price);
        } else {
            $this->storePrice($price);
        }
    }

    private function getStoredPrice($company, $identifier)
    {
        return $this->entityManager->getRepository('PriceContext:Price')->findOneBy([
            'company' => $company,
            'identifier' => $identifier
        ]);
    }

    private function updatePrice(Price $storedPrice, Price $newPrice)
    {
        $storedPrice->setValue($newPrice->getValue());
        $this->entityManager->flush();
    }

    private function storePrice($price)
    {
        $this->entityManager->persist($price);
        $this->entityManager->flush();
    }
}