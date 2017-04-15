<?php

namespace Price\Filter;

class FilteredData
{
    public $value;

    /**
     * FilteredData constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}