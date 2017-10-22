*** Settings ***
Library  Selenium2Library

*** Variables ***
${BROWSER}      phantomjs

*** Test Cases ***

Simple Test
    Create Webdriver  PhantomJS
    Go To  http://${USER}:${PASS}@${URL}/login
    Page Should Not Contain    "Maciej"
    Page Should Contain    Stock Exchange Monitor