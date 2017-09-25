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

    public function pullPrice($marketId, $date)
    {
        try {
            $rawData = $this->downloader->download($date);
            $filteredData = $this->filter->filter($rawData, $marketId);
            $price = $this->parser->parse($filteredData);

            $this->storePrices($price);
        } catch (Downloader\NoFileException $e) {

        }
    }

    /**
     * @param Price $price
     */
    private function storePrices($price)
    {
        $this->entityManager->persist($price);
        $this->entityManager->flush();
    }
}