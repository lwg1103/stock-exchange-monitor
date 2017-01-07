<?php

namespace Price\Downloader;

use GuzzleHttp\Exception\ClientException;
use Price\Downloader;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;

class BossaDownloader implements Downloader
{
    /** @var Client */
    private $client;
    /** @var Filesystem */
    private $fileSystem;

    const URL = "http://bossa.pl/pub/metastock/mstock/sesjaall/";
    const FILE_TEMPLATE = "%s.prn";

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
        if ($this->fileIsCached($date)) {
            $fileContent = $this->readCache($date);
        } else {
            $fileContent = $this->downloadFile($date);
            $this->cacheFile($date, $fileContent);
        }

        return new RawData($fileContent);
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
        return sprintf(self::FILE_TEMPLATE, $date->format("Ymd"));
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
        return __DIR__ . '/../../../var/cache/' . $this->getFileName($date);
    }
}