Feature: SOAP API
  In order to demonstrate how to build own API Client with SOAP API library
  As a developer
  I implement IP2Geo webservice client

  Scenario: Check Geo Location by IP (Simple Client)
    Given Given "Simple" IP2Geo Client API
    When I ask for geo location for IP "8.8.8.8"
    Then the geo location should be returned

  Scenario: Check Geo Location by IP (Input normalising Client)
    Given Given "InputNormalising" IP2Geo Client API
    When I ask for geo location for IP "8.8.8.8"
    Then the geo location should be returned

  Scenario: Check Geo Location by IP (Result hydrating Client)
    Given Given "ResultHydrating" IP2Geo Client API
    When I ask for geo location for IP "8.8.8.8"
    Then the geo location should be returned
