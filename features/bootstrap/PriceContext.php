<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

require_once 'vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class PriceContext implements Context, SnippetAcceptingContext
{
    /**
     * @Then I see :arg1 company price
    */
    public function iSeeCompanyPrice($arg1)
    {
        throw new PendingException();
    }
}
