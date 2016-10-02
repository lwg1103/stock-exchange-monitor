<?php

namespace Price\Downloader;

class RawData
{
    /** @var string[] */
    public $rows;

    /**
     * RawData constructor.
     */
    public function __construct($fileContent)
    {
        $this->convertToArray($fileContent);
        $this->trimEachRow();
    }
    
    private function convertToArray($fileContent)
    {
        $this->rows = explode("\n", $fileContent);
    }

    private function trimEachRow()
    {
        $this->rows = array_map(
            function ($row) {
                return trim($row);
            }, 
            $this->rows
        );
    }
}