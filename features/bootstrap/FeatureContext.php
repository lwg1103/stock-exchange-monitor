<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Carbon\Carbon;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @Given I am a User
     */
    public function iAmAUser()
    {
        /**
         * @todo fill when logging module is implemented
         */
    }

    /**
     * @Given the time is :relativeTime
     */
    public function theTimeIs($relativeTime)
    {
        Carbon::setTestNow();
        Carbon::setTestNow(new Carbon($relativeTime));
    }
}
