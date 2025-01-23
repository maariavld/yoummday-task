# ❗ Please do not fork this repository ❗

# Yoummday Refactoring Task
This project provides an example of a refactored `/has_permission/{token}` endpoint demonstrating improved code quality, modularity, testability and the integration of Symfony components.

# Requirements
- php 8.3
- composer
- Docker
- Docker Compose (optional, but recommended)

## Clone the repository:
   ```shell
   git clone https://github.com/maariavld/yoummday-task.git
   cd yoummday-task
   ```

## Installation with Docker and Docker Compose

**Build the Docker image:**
   ```shell
   docker build -t yoummday-refactoring .
   ```

## Running the Application

1.
   a. **Using Docker:**
   ```shell
   docker run --name yoummday-refactoring-app -p 1337:1337 yoummday-refactoring
   ```

   b. **Using Docker Compose:**
   ```shell
    docker-compose up -d
   ```
   
2. **Curl command to access the application:**
    ```shell
   curl --location --request GET http://0.0.0.0:1337/has_permission \
   --header 'Authorization: Bearer token1234'
    ```

***Expected output:***

**Initial output**
```shell

[INFO] Registering GET /has_permission
[INFO] Server running on 0.0.0.0:1337
```
**After running the curl command**
```shell
[INFO] Dispatching GET /has_permission
app.INFO: Validating token [] []
app.INFO: Token found [] []

```

## Installation without Docker
```shell
$ composer install
```

## Run
```shell 
$ php src/main.php
```
***Expected output:***

**Initial output**
```shell

[INFO] Registering GET /has_permission
[INFO] Server running on 0.0.0.0:1337
```
**After running the curl command**
```shell
[INFO] Dispatching GET /has_permission
app.INFO: Validating token [] []
app.INFO: Token found [] []

```

## Curl command to access the application:
```shell
curl --location --request GET http://0.0.0.0:1337/has_permission \
--header 'Authorization: Bearer token1234'
```

# Testing outside container
```shell
$ php vendor/bin/phpunit Test
```

# Testing inside container
```shell
docker run --rm yoummday-task-app vendor/bin/phpunit Test
```

## Key Changes

* **Cleaner, More Modular Code:** 
  - The `PermissionHandler` is now more concise, readable and maintainable. 
  - Unnecessary logic has been removed, helper functions have been introduced and dependency injection is used. 
  - Named constants for permissions and status codes enforce consistency.
* **Improved Testability:** 
  - The code is now significantly easier to test due to improved modularity and dependency injection.
* **Comprehensive Logging:** 
  - Logging using Monolog for better insights into application behavior and debugging.
* **Improved Error Handling:** 
  - More specific and informative error responses (401 Unauthorized for missing token, 403 Forbidden if token has no permission) improve the API's usability.
* **Updated Tests:** 
  - The test suite has been expanded to cover more edge cases and error conditions, improving overall test coverage.
* **Dockerized Environment:** 
  - The application now runs within a Docker container for easier setup and deployment.

## Further Improvements
Given more time, the following improvements would further improve the application's architecture, maintainability and scalability:

- Symfony WebServerBundle: Symfony's built-in web server for local development and testing, simplifying setup and configuration. (Current Implementation: Requires manual PHP server setup, which can be error-prone and time-consuming.)
- Symfony Config Component: Symfony's Config component to manage application settings (database credentials, API keys, etc.) in config/packages/ or .env files. (Current Implementation: Configuration is hardcoded, making it less flexible and harder to manage across different environments.)
- Symfony Security Component: Robust authentication and authorisation using Symfony's Security component. Use JWT authentication (e.g., with lexik/jwt-authentication-bundle) and voters for fine-grained access control. (Current Implementation: Basic token validation lacks essential security features like password hashing, brute-force protection and role-based access control.)
- Symfony Routing: Define routes using annotations, YAML files, or attributes for improved flexibility and maintainability. Use route parameters and groups for better organisation. (Current Implementation: Routing is handled by attributes, which can become less manageable for complex applications.)
- Symfony Service Container and Autowiring: Register services in config/services.yaml and use autowiring to minimise boilerplate code and improve dependency management. (Current Implementation: Manual dependency injection in PermissionHandler can become cumbersome.)
- Doctrine ORM (If Applicable): If using a database, integrate Doctrine ORM for object-relational mapping, simplifying database interactions and improving code structure.
- Monolog Integration: Configure Monolog for structured logging and integrate it with Symfony's logging framework. Use different log levels (debug, info, error, etc.) for better control and analysis. (Current Implementation: Basic logging lacks context and structured formatting.)
- Code Style and Linters: Implement consistent code style with PHP CS Fixer and use a static analysis tool like PHPStan to catch potential errors early on. (Current Implementation: Inconsistent code style and lack of static analysis can lead to maintainability issues and bugs.)
- Comprehensive Testing: Expand the test suite with more unit, integration and functional tests, ensuring better coverage and confidence in the code. (Current Implementation: Limited tests might not cover all edge cases and potential errors.)
- APIPlatform Integration: If building a RESTful API, APIPlatform could be a suitable option for automatic CRUD generation, pagination, filtering and validation. (Current Implementation: Basic API endpoint lacks advanced features and scalability.)
- Redis/Memcached Integration: Implement Redis or Memcached for caching and session management. (Current Implementation: No caching mechanism, which can impact performance and scalability.)
- Continuous Integration/Deployment: Set up CI/CD pipelines with tools like GitHub Actions, GitLab CI, or Jenkins for automated testing, linting and deployment. (Current Implementation: Manual testing and deployment can be error-prone and time-consuming.)
- Symfony Event Dispatcher: Use Symfony's Event Dispatcher component for decoupling components and implementing event-driven architecture in case granting access should trigger additional actions like sending notifications. (Current Implementation: No event handling mechanism, which can lead to tight coupling and reduced flexibility.)

Quick wins would be:
- Using Symfony config for managing application settings like service autowiring, security settings, routing, framework configuration, etc.
- Refactoring the PermissionHandler into a Symfony Controller and Service for better separation of concerns and testability.
- Add roles and permissions in the security configuration and firewall to enforce access control based on user roles.
- Using Symfony Kernel and index.php for bootstrapping the application and handling requests instead of the current approach with a main.php file that builds the server using a custom framework which could be error-prone and less maintainable if it is less adopted or maintained over time.
- Using Symfony HTTP client for making HTTP requests and handling responses as it provides more features and flexibility.
- Enforcing consistent code style with PHP CS Fixer and using PHPStan for static analysis.
