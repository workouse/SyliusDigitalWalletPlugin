@managing_wallet
Feature: Adding credit
  As a Store Owner

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui
  Scenario: Adding credit
    Given the store has customer "omer@eresbiotech.com"
    And I go to the create credit with this customer
    And I fill the Amount with "100"
    And I fill the Currency Code with "USD"
    And I fill the Action with "test"
    And I try to add it
    Then I should be notified that the credit has been created

  @ui
  Scenario: Adding new credit with blank data
    Given the store has customer "omer@eresbiotech.com"
    And I go to the create credit with this customer
    And I add it
    And I should be notified that "Amount, Action" fields cannot be blank
