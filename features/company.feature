Feature: User can check company details and price

  Scenario: I want to see company details as a User
    Given I am a User
    When  I list companies
    Then  I see all companies in the system