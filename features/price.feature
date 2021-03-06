Feature: each company on the stock market has price history attached

  Background:
    Given the time is "last saturday"

  Scenario: I want to see last share price at company details as a User
       When I enter "PKO" company price site
       Then I see one current company price
        And The current company price is "1.24"

  Scenario: I want to see all share prices at company details as a User
       When I enter "PKO" company price site
       Then I see "4" company prices

  Scenario: I want to pull share price for given Company as an Admin
      Given There are no prices for "yesterday"
       When I run script that pull price for "PKO"
       Then I see "PKO" company price downloaded for "yesterday"

  Scenario: I want to pull share price for given Company as an Admin and update existing
      Given There are no prices for "yesterday"
        And There is price with value "0.01" for "PKO" for "yesterday"
       When I run script that pull price for "PKO"
       Then I see "PKO" company price downloaded for "yesterday"
        And I see price value for "PKO" for "yesterday" is not "0.01" anymore

  Scenario: I want to pull all share prices as an Admin
      Given There are no prices for "yesterday"
       When I run script that pull all prices
       Then I see all company prices downloaded for "yesterday"