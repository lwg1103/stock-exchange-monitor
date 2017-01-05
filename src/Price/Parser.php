<?php

namespace Price;

use Price\Filter\FilteredData;
use Price\Entity\Price;

interface Parser
{
    /**
     * @param FilteredData $filteredData
     * 
     * @return Price
     */
    public function parse(FilteredData $filteredData);
}