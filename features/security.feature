Feature: It's required to have specific roles to perform some actions

  Scenario: Anonymous visitor CAN"T use application
      Given I am on "/"
       Then I should be on "/login"

  Scenario: As a User I CAN use application
      Given I am a User
       When I am on "/"
       Then I should be on "/"
        And I should see "Stock Exchange Monitor"

  Scenario: As a User I CAN'T add the report
      Given I am a User
       When I am on "/report/add"
       Then the response status code should be 403

  Scenario: As a Admin I CAN add the report
      Given I am an Admin
       When I am on "/report/add"
       Then the response status code should be 200
