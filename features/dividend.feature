Feature: each company on the stock market has dividends history attached

  Scenario: I want to see all dividends at company details as a User
       When I enter "PKO" company dividend site
       Then I see at least "4" company dividends