Feature: I want see user page

  Scenario: User page
    Given I am on "/admin/user"
    Then I should see "user!"
