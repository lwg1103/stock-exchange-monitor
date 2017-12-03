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

  Scenario: I want to see average dividend for last 7 years for the company
    Given "ACP" company has dividend for period from "01-01-2017" to "31-12-2017" with a rate "2.0"
    And  "ACP" company has dividend for period from "01-01-2016" to "31-12-2016" with a rate "3.0"
    And  "ACP" company has dividend for period from "01-01-2015" to "31-12-2015" with a rate "0.5"
    And  "ACP" company has dividend for period from "01-01-2014" to "31-12-2014" with a rate "1.0"
    And  "ACP" company has dividend for period from "01-01-2010" to "31-12-2010" with a rate "200.0"
    #8 years ago
    And  "ACP" company has dividend for period from "01-01-2013" to "31-12-2013" with a rate "1.2"
    And  "ACP" company has dividend for period from "01-01-2012" to "31-12-2012" with a rate "0.8"
    And  "ACP" company has dividend for period from "01-01-2011" to "31-12-2011" with a rate "2.0"
    When I check dividend rate for last 7 years for "ACP" company
    Then I see "1.5" dividend rate

  Scenario: I want to see average dividend for last 7 years for the company who doesn't have dividend history for last 7 years
    #take 0 for year when dividend is missing
    Given "PZU" company has dividend for period from "01-01-2017" to "31-12-2017" with a rate "2.0"
    And  "PZU" company has dividend for period from "01-01-2016" to "31-12-2016" with a rate "3.2"
    And  "PZU" company has dividend for period from "01-01-2015" to "31-12-2015" with a rate "1.5"
    # 0 for 2014
    And  "PZU" company has dividend for period from "01-01-2013" to "31-12-2013" with a rate "1.0"
    # 0 for 2012 and 2011
    When I check dividend rate for last 7 years for "PZU" company
    Then I see "1.1" dividend rate

  Scenario: I want to see average dividend for last 7 years for the company who pay dividends more then once a year
    #not implemented yet (#610)