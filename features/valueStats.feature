Feature: User can check different value stats

  Scenario: I want to see total company value
    And  I add "31-12-2017" report manually for "PKO" company
            #netProfit=7, sharesQuantity=9
        And "PKO" company current price is "120"
       When I check total company value for "PKO"
       Then I should see "1080" total company value

  Scenario: I want to see C/Z value for the last year
    And  I add "31-12-2018" report manually for "PKO" company
            #netProfit=7, sharesQuantity=9
        And "PKO" company current price is "93"
       When I check C/Z value for "PKO"
       Then I should see "15.5" C/Z value

