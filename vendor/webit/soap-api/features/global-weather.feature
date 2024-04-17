Feature: SOAP API
  In order to demonstrate how to build own API Client with SOAP API library
  As a developer
  I implement Global Weather webservice client

  Background:
    Given Global Weather Client API

  Scenario: Fetching cities list
    When I ask for list of cities in "Poland"
    Then List of the cities should be returned

  Scenario: Getting current weather
    When I ask for weather for "Warszawa" in "Poland"
    Then Current weather should be returned
