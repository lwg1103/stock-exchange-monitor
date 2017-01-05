Feature: Each company has reports stored. User can CRUD them.

    Background:
        Given I am a User

    Scenario: I want to list reports for given company
        When  I check reports for "PKO" company
        Then  I see all reports for "PKO" company

    Scenario: I want to add report manually
        When  I check reports for "PKO" company
         And  I add report manually for "PKO" company
        Then  I see one additional report for "PKO" company

    Scenario: I want to see particular report details
         When I check "quarterly" report for "PKO" with identifier "31-03-2016"
         Then I should see "NetProfit" at "20" in the result report
          And I should see "Income" at "500" in the result report
          And I should see "Assets" at "330" in the result report
          And I should see "CurrentAssets" at "150" in the result report
          And I should see "Liabilities" at "200" in the result report
          And I should see "CurrentLiabilities" at "100" in the result report
          And I should see "auto" report type

    Scenario: I want to see manual report if both manual and auto are available
         When I check "annual" report for "PKO" with identifier "31-12-2015"
         Then I should see "manual" report type

    Scenario: I want to edit report manually

    Scenario: I want to pull report automatically