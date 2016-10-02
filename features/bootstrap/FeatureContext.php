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
     * @Given /^the time is "([^"]*)"$/
     */
    public function theTimeIs($arg1)
    {
        Carbon::setTestNow(Carbon::createFromFormat("Y-m-d h:i", $arg1));
    }
}
