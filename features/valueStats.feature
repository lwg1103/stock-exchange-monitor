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
        And "PKO" company current price is "120"
            #totalValue 1080
       When I check C/Z for last year value for "PKO"
       Then I should see "0.15" C/Z value

  Scenario: I want to see C/Z value for the last four quarters
    And  I add "31-06-2019" quarterly report manually for "PKO" company
    And  I add "31-09-2019" quarterly report manually for "PKO" company
    And  I add "31-12-2019" quarterly report manually for "PKO" company
    And  I add "31-03-2020" quarterly report manually for "PKO" company
    And  I add "31-06-2020" quarterly report manually for "PKO" company
    And  I add "31-09-2020" quarterly report manually for "PKO" company
            #netProfit=7 EACH so 28 for a year, sharesQuantity=9
    And "PKO" company current price is "120"
            #totalValue 1080
    When I check C/Z for last 4Q value for "PKO"
    Then I should see "0.04" C/Z value

