# Telecommunication Plans & Applications API

- This repository contains a Laravel 11 project and requires PHP 8.1 or higher and Composer installed.
- The project follows Laravel conventions and includes PHP unit tests
- This setup demonstrates building a scalable API system using Laravel, queues, and testing best practices.
--- 
### Getting Started
- 1 Clone this repository.
- 2 Copy .env.example to .env.
- 3 Run composer install.


--- 

- Database:  SQLite 
- Tests: PHPUnit (The project uses an in-memory SQLite database for simplicity.)
    - To run the tests, execute:
     `php artisan test`
--- 
### Features
### 1. Applications Listing API Endpoint
- This API provides a paginated list of all applications in the system, with an optional filter by plan type (null, nbn, opticomm, mobile).
- Accessible only to authenticated users.
- Applications are listed with the oldest first.
- Designed to be consumed by a Vue 3 SPA frontend.
- The endpoint returns the following fields:
    - Application ID
    - Customer full name
    - Address
    - Plan type
    - Plan name
    - State
    - Plan monthly cost (stored in cents in the database, displayed in human-readable dollar format)
    - Order ID (visible only for applications with a complete status)

### 2. NBN Application Automation
- Automated workflow for processing NBN applications:
	- Runs every 5 minutes for applications with status = order.
	- Each application is processed via a queue worker.
	- The workflow updates the application table with an OrderId and:
	- Marks the application as complete if successful.
	- Marks the application as failed if an error occurs.

- Applications can be “ordered” using a simulated B2B integration. The workflow sends a POST request with the following details:
	- address_1
	- address_2
	- city
	- state
	- postcode
	- plan
	- name

- Note: No real HTTP requests are sent. Sample success and failure are used from tests/stubs.

--- 
### ✨ Feature and Test Checklist

Below is a list of features and tests:

✅ : Done
➖ :  Not Required


| Description                                                                                                             | Feature Status | Test Status |
|-------------------------------------------------------------------------------------------------------------------------|----------------|-------------|
| Feature 1 - Applications Listing API Endpoint                                                             |                |             |
| API endpoint to list all applications.                                                            | ✅             | ✅          |
| An optional plan type filter (null, nbn, opticomm, mobile).                                                       | ✅             | ✅          |
| The endpoint will exist only for authenticated users.                                                                   | ✅             | ➖          |
| Only providing limited data                                     | ✅             | ✅          |
| Order ID field is only for applications with status "complete."                                    | ✅             | ✅          |
| The data returned is paginated.                                                                                   | ✅             | ✅          |
| The oldest applications are at the top of the list.                                                                  | ✅             | ✅          |
| The plan monthly cost is converted to dollar format from cents.                                                   | ✅             | ✅        |
|                                                                                                                         |                |             |
| Feature 2 - NBN Application Automation                                                                   |                |             |
| Only applications with order status and plan type "nbn" can be ordered       =      | ✅             | ✅          |
| Pick up and process nbn applications every 5 minutes.                                                                    | ✅             | ➖          |
| Each application must be processed on a queue.                                                                           | ✅             | ✅          |
| If the application POST request is successful, store the Order ID for that application.                                  | ✅             | ✅          |
| If the application POST request is successful, update the status to "complete" for that application.                     | ✅             | ✅          |
| If the application POST request fails, update the status to "order failed" or "error"  | ✅             | ✅          |
| Sending an Http::post request to with given application and plan details | ✅             | ➖          |
| Using sample successful and failure responses from `test\stubs`.                                                           | ✅             | ✅          |