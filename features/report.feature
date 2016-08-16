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

    Scenario: I want to edit report manually

    Scenario: I want to pull report automatically