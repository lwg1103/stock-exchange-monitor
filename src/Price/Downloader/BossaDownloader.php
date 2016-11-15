<?php

namespace Price\Downloader;

use GuzzleHttp\Exception\ClientException;
use Price\Downloader;
use GuzzleHttp\Client;

class BossaDownloader implements Downloader
{
    const URL_TEMPLATE = "http://bossa.pl/pub/metastock/mstock/sesjaall/%s.prn";

    /**
     * {@inheritdoc}
     */
    public function download(\DateTime $date)
    {
        $client = $this->getHttpClient($this->getURL($date));
        try {
            $response = $client->get('');
        } catch (ClientException $e) {
            throw new NoFileException();
        }

        return new RawData($response->getBody());
    }

    private function getURL(\DateTime $date)
    {
        return sprintf(self::URL_TEMPLATE, $date->format("Ymd"));
    }

    /**
     * @param $url
     *
     * @return Client
     */
    private function getHttpClient($url)
    {
        return new Client(['base_uri' => $url]);
    }
}