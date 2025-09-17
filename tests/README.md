# Zaa Radio Test Suite

This directory contains the comprehensive test suite for the Zaa Radio Advertisement Booking System.

## Test Structure

The test suite is organized into three main categories:

### Unit Tests (`tests/Unit/`)
- **UserTest.php** - Tests for the User model
- **SlotTest.php** - Tests for the Slot model  
- **BookingTest.php** - Tests for the Booking model
- **SessionTest.php** - Tests for the Session utility
- **SecurityMiddlewareTest.php** - Tests for security middleware

### Integration Tests (`tests/Integration/`)
- **AuthControllerTest.php** - Tests for authentication controller
- **BookingControllerTest.php** - Tests for booking controller
- **SlotControllerTest.php** - Tests for slot controller
- **UserManagementControllerTest.php** - Tests for user management controller
- **ReportsControllerTest.php** - Tests for reports controller

### Feature Tests (`tests/Feature/`)
- **BookingWorkflowTest.php** - End-to-end booking workflow tests
- **UserManagementWorkflowTest.php** - User management workflow tests
- **SecurityTest.php** - Security feature tests
- **ApiTest.php** - API endpoint tests

## Running Tests

### Prerequisites

1. Install PHPUnit:
```bash
composer install
```

2. Set up test database:
```bash
mysql -u root -p -e "CREATE DATABASE zaa_radio_test;"
```

3. Configure test environment:
```bash
cp .env.example .env.testing
# Edit .env.testing with test database credentials
```

### Running All Tests

```bash
# Using the test runner script
php tests/run_tests.php

# Or using PHPUnit directly
vendor/bin/phpunit tests/ --configuration tests/phpunit.xml
```

### Running Specific Test Suites

```bash
# Run only unit tests
vendor/bin/phpunit tests/Unit/ --configuration tests/phpunit.xml

# Run only integration tests
vendor/bin/phpunit tests/Integration/ --configuration tests/phpunit.xml

# Run only feature tests
vendor/bin/phpunit tests/Feature/ --configuration tests/phpunit.xml
```

### Running Individual Tests

```bash
# Run specific test class
vendor/bin/phpunit tests/Unit/UserTest.php --configuration tests/phpunit.xml

# Run specific test method
vendor/bin/phpunit tests/Unit/UserTest.php::testCreateUser --configuration tests/phpunit.xml
```

## Test Configuration

### PHPUnit Configuration (`tests/phpunit.xml`)

- **Bootstrap**: `tests/bootstrap.php`
- **Test Suites**: Unit, Integration, Feature
- **Coverage**: Includes `app/` directory, excludes email utilities
- **Environment**: Testing environment with test database

### Test Database (`tests/TestDatabase.php`)

- Automatically sets up test database
- Runs migrations
- Seeds test data
- Cleans up after tests

### Test Bootstrap (`tests/bootstrap.php`)

- Loads Composer autoloader
- Sets up testing environment
- Mocks global functions for testing
- Defines constants for testing

## Test Data

The test suite uses a dedicated test database (`zaa_radio_test`) with the following test data:

### Test Users
- **Admin**: admin@test.com / password123
- **Manager**: manager@test.com / password123  
- **Advertiser**: advertiser@test.com / password123

### Test Slots
- Morning Drive Slot (6:00 AM - 9:00 AM) - Available
- Midday Slot (12:00 PM - 2:00 PM) - Available
- Evening Rush Slot (5:00 PM - 7:00 PM) - Booked

### Test Bookings
- Pending booking for Morning Drive Slot
- Approved booking for Midday Slot

## Writing Tests

### Unit Test Example

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Tests\TestDatabase;

class UserTest extends TestCase
{
    private $userModel;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->userModel = new User();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    public function testCreateUser()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'advertiser'
        ];

        $userId = $this->userModel->createUser($userData);
        
        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);
    }
}
```

### Integration Test Example

```php
<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use Tests\TestDatabase;

class AuthControllerTest extends TestCase
{
    private $authController;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->authController = new AuthController();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    public function testSuccessfulLogin()
    {
        $_POST = [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'csrf_token' => 'valid_token'
        ];

        $_SESSION = ['csrf_token' => 'valid_token'];

        $this->authController->login();

        $this->assertTrue($_SESSION['logged_in'] ?? false);
    }
}
```

## Test Coverage

The test suite aims for comprehensive coverage of:

- **Models**: All CRUD operations, validation, and business logic
- **Controllers**: All endpoints, error handling, and responses
- **Utilities**: Session management, security, and helper functions
- **Middleware**: Authentication, authorization, and security checks
- **Workflows**: End-to-end user journeys and business processes

## Continuous Integration

The test suite is designed to run in CI/CD pipelines:

```yaml
# GitHub Actions example
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php tests/run_tests.php
```

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Ensure test database exists
   - Check database credentials in `.env.testing`
   - Verify MySQL service is running

2. **Permission Errors**
   - Ensure test database user has proper permissions
   - Check file permissions for test directories

3. **Memory Issues**
   - Increase PHP memory limit: `php -d memory_limit=512M tests/run_tests.php`
   - Run tests in smaller batches

4. **Test Failures**
   - Check test database state
   - Verify test data is properly seeded
   - Review test isolation

### Debug Mode

Run tests with verbose output:

```bash
vendor/bin/phpunit tests/ --configuration tests/phpunit.xml --verbose
```

## Contributing

When adding new features:

1. Write unit tests for new models and utilities
2. Write integration tests for new controllers
3. Write feature tests for new workflows
4. Ensure all tests pass before submitting
5. Update test documentation as needed

## Test Metrics

- **Total Tests**: 100+ tests
- **Coverage Target**: 90%+ code coverage
- **Execution Time**: < 30 seconds
- **Test Categories**: Unit, Integration, Feature
- **Database**: Isolated test database
- **Mocking**: Global functions and external dependencies

