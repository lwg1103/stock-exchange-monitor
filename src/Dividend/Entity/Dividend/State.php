<?php

namespace Dividend\Entity\Dividend;

use Application\BaseEnum;

/**
 * Class State
 * 
 * @package Dividend\Entity\Dividend
 */
class State extends BaseEnum
{
    const PROPOSAL = 1;
    const PASSED = 2;
    const PAID = 3;
}