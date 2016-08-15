Feature: User can check company details and price

  Background:
    Given I am a User

  Scenario: I want to see all companies as a User
    When  I list companies
    Then  I see all companies in the system

  Scenario: I want to see company details as a User
    When  I enter "PKO" company site
    Then  I get "PKO" company details