*** Settings ***

Library  Selenium2Library

*** Variables ***

${CREDENTIALS}  key:secret
${BROWSER}      phantomjs

*** Test Cases ***

Simple Test
    Create Webdriver  PhantomJS
    Go To  http://test.stock.t.x-coding.pl/login
    Page Should Not Contain    "Maciej"
    Page Should Contain    Login
