@managing_wallet
Feature: Using Wallet
  As a Customer

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Angel T-Shirt" priced at "$39.00"
    And the store ships everywhere for free
    And the store allows paying with "Cash on Delivery"
    And there is a user "omer@eresbiotech.com"
    And I have "100" "USD" credit with "omer@eresbiotech.com"
    And I am logged in as "omer@eresbiotech.com"

  @ui
  Scenario: I see my credit
    When I go to the account page
    And I see my credit

  @ui
  Scenario: Using Wallet
    Given I added product "Angel T-Shirt" to the cart
    And I use my credit
    Then I should be notified that the credit has been used
    Then My cart's total should "$38.00"

  @ui
  Scenario: Removing Wallet
    Given I added product "Angel T-Shirt" to the cart
    And I remove my credit
    Then I should be notified that the credit has been removed
    Then My cart's total should "$39.00"
