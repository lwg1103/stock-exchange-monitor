Feature: each company on the stock market has price history attached

  Background:
    Given the time is "2016-12-09 01:00"

  Scenario: I want to see last share price at company details as a User
     When I enter "PKO" company price site
     Then I see one current company price
      And The current company price is "123" "PLN"

  Scenario: I want to see all share prices at company details as a User
     When I enter "PKO" company price site
     Then I see "3" company prices

  Scenario: I want to pull share price for given Company as an Admin
    Given There are no prices for "2016-12-08"
    When I run script that pull price for "PKO"
    Then I see "PKO" company price downloaded for "2016-12-08"

  Scenario: I want to pull all share prices as an Admin
    Given There are no prices for "2016-12-08"
     When I run script that pull all prices
     Then I see all company prices downloaded for "2016-12-08"