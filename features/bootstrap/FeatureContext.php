<?php

use Behat\Behat\Context\Context;
use Carbon\Carbon;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @Given the time is :relativeTime
     */
    public function theTimeIs($relativeTime)
    {
        Carbon::setTestNow();
        Carbon::setTestNow(new Carbon($relativeTime));
    }
}
