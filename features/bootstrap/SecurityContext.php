<?php

use Behat\Behat\Context\Context;

class SecurityContext extends \Behat\MinkExtension\Context\MinkContext implements Context
{
    /**
     * @Given I am a User
     */
    public function iAmAUser()
    {
        $this->visit("/");
        $this->fillField('username', 'user');
        $this->fillField('password', 'user');
        $this->pressButton('_submit');
    }
    /**
     * @Given I am an Admin
     */
    public function iAmAnAdmin()
    {
        $this->visit("/");
        $this->fillField('username', 'admin');
        $this->fillField('password', 'admin');
        $this->pressButton('_submit');
    }
}