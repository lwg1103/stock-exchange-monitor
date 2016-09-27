Feature: each company on the stock market has price history attached

  Scenario: I want to see last share price at company details as a User
    When I enter "PKO" company price site
    Then I see "PKO" current company price
     And The current "PKO" company price is "123" "PLN"

  Scenario: I want to see all share prices at company details as a User
    When I enter "PKO" company price site
    Then I see "PKO" "3" company prices