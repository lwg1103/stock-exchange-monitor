<?php

namespace Price\Downloader;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Price\Downloader;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class BossaDownloader implements Downloader
{
    /** @var Client */
    private $client;
    /** @var Filesystem */
    private $fileSystem;

    const URL = "http://bossa.pl/pub/metastock/mstock/sesjaall/";
    const FILE_TEMPLATE = "%s.prn";
    const ZIP_FILE_TEMPLATE = "%s.zip";

    /**
     * BossaDownloader constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->fileSystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function download(\DateTime $date)
    {
        if ($this->isCurrentMonth($date)) {
            return $this->downloadThisMonthData($date);
        } elseif ($this->isCurrentYear($date)) {
            return $this->downloadThisYearData($date);
        } else {
            return $this->downloadPreviousYearsData($date);
        }
    }

    private function isCurrentMonth(\DateTime $date)
    {
        $now = Carbon::createFromTimestamp($date->getTimestamp());

        return $now->isCurrentMonth();
    }

    private function isCurrentYear(\DateTime $date)
    {
        $now = Carbon::createFromTimestamp($date->getTimestamp());

        return $now->isCurrentYear();
    }

    private function downloadThisMonthData(\DateTime $date)
    {
        $fileContent = $this->downloadFileFromBossa($date);

        return new RawData($fileContent);
    }

    private function downloadThisYearData(\DateTime $date)
    {
        $this->downloadFileFromBossa($date);

        $fileContent = $this->unZipMonthlyArchive( $this->getCacheFileLocation($date), $date);

        return new RawData($fileContent);
    }

    private function downloadPreviousYearsData(\DateTime $date)
    {
        $this->downloadFileFromBossa($date);

        $this->unZipYearlyArchive($date);

        $fileContent = $this->unZipMonthlyArchive( $this->getTmpFileLocation(), $date);

        return new RawData($fileContent);
    }

    private function downloadFileFromBossa(\DateTime $date)
    {
        if ($this->fileIsCached($date)) {
            $fileContent = $this->readCache($date);
            return $fileContent;
        } else {
            $fileContent = $this->downloadFile($date);
            $this->cacheFile($date, $fileContent);
            return $fileContent;
        }
    }

    /**
     * @param \DateTime $date
     * 
     * @return bool
     */
    private function fileIsCached(\DateTime $date)
    {
        return $this->fileSystem->exists($this->getCacheFileLocation($date));
    }

    /**
     * @param \DateTime $date
     *
     * @return mixed
     */
    private function readCache(\DateTime $date)
    {
        return file_get_contents($this->getCacheFileLocation($date));
    }

    /**
     * @param \DateTime $date
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    private function downloadFile(\DateTime $date)
    {
        try {
            return $this->getHttpClient()->get($this->getURL($date))->getBody();
        } catch (ClientException $e) {
            throw new NoFileException();
        }
    }

    /**
     * @param \DateTime $date
     * @param $fileContent
     */
    private function cacheFile(\DateTime $date, $fileContent)
    {
        try {
            $this->fileSystem->dumpFile($this->getCacheFileLocation($date), $fileContent);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at " . $e->getPath();
        }
    }

    private function getURL(\DateTime $date)
    {
        return self::URL . $this->getFileName($date);
    }

    private function getFileName(\DateTime $date)
    {
        if ($this->isCurrentMonth($date)) {
            return sprintf(self::FILE_TEMPLATE, $date->format("Ymd"));
        } elseif ($this->isCurrentYear($date)) {
            return sprintf(self::ZIP_FILE_TEMPLATE, $date->format("m-Y"));
        } else {
            return sprintf(self::ZIP_FILE_TEMPLATE, $date->format("Y"));
        }
    }

    /**
     * @return Client
     */
    private function getHttpClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    private function getCacheFileLocation(\DateTime $date)
    {
        return __DIR__ . '/../../../var/cache/prices/' . $this->getFileName($date);

    }

    /**
     * @return string
     */
    private function getTmpFileLocation()
    {
        return __DIR__ . '/../../../var/cache/prices/tmp';
    }

    private function unZipMonthlyArchive($location, \DateTime $date)
    {
        return file_get_contents("zip://" . $location . "#" . $date->format("Ymd") . ".prn");
    }

    private function unZipYearlyArchive(\DateTime $date)
    {
        $monthlyZip = file_get_contents("zip://" . $this->getCacheFileLocation($date) . "#" . $date->format("m-Y") . ".zip");
        $this->fileSystem->dumpFile($this->getTmpFileLocation(), $monthlyZip);
    }
}