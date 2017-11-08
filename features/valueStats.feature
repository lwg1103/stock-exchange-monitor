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

  Scenario: I want to see C/Z value for last 7 years
    When I add "31-12-2020" annual report manually for "ACP" company with netProfit "500"
    And  I add "31-12-2021" annual report manually for "ACP" company with netProfit "5"
    And  I add "31-12-2022" annual report manually for "ACP" company with netProfit "5"
    And  I add "31-12-2023" annual report manually for "ACP" company with netProfit "5"
    And  I add "31-12-2024" annual report manually for "ACP" company with netProfit "5"
    And  I add "31-12-2025" annual report manually for "ACP" company with netProfit "7.8"
    And  I add "31-12-2026" annual report manually for "ACP" company with netProfit "5"
    And  I add "31-12-2027" annual report manually for "ACP" company with netProfit "5"
            #avg netProfit=5.4k, sharesQuantity=9
    And "ACP" company current price is "120"
            #totalValue 1080
    When I check C/Z for last 7 years value for "ACP"
    Then I should see "0.2" C/Z value

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

  Scenario: I want to see C/WK value for the last year
    And  I add "31-12-2019" report manually for "PKO" company
            #bookValue=2, sharesQuantity=9
    And "PKO" company current price is "120"
    When I check C/WK for last year value for "PKO"
    Then I should see "0.54" C/WK value

  Scenario Outline: I want to see if company had loss in net profit in last 7 years
    When I add "31-12-2019" annual report manually for <company> company with netProfit "-5"
    And I add "31-12-2020" annual report manually for <company> company with netProfit "5"
    And I add "31-12-2021" annual report manually for <company> company with netProfit "5"
    And I add "31-12-2022" annual report manually for <company> company with netProfit "5"
    And I add "31-12-2023" annual report manually for <company> company with netProfit "5"
    And I add "31-12-2024" annual report manually for <company> company with netProfit <third_year_profit>
    And I add "31-12-2025" annual report manually for <company> company with netProfit "5"
    And I add "31-12-2026" annual report manually for <company> company with netProfit "5"
    Then I see it is <result> that <company> had no loss in net profit in last seven years

    Examples:
      | company | third_year_profit | result |
      | ELB     | 5                 | true   |
      | PZU     | -1                | false  |