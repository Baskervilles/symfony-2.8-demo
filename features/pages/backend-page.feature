Feature: I want see admin page

  Scenario: Admin page
    Given I am on "/admin"
    Then I should see "backend!"
