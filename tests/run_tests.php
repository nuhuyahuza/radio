<?php

/**
 * Test Runner
 * Runs the test suite and displays results
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up test environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_DATABASE'] = 'zaa_radio_test';

// Load environment variables
if (file_exists(__DIR__ . '/../.env.testing')) {
    $lines = file(__DIR__ . '/../.env.testing', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Check if PHPUnit is available
if (!class_exists('PHPUnit\Framework\TestCase')) {
    echo "PHPUnit is not installed. Please run: composer install\n";
    exit(1);
}

// Run tests
$command = 'vendor/bin/phpunit tests/ --configuration tests/phpunit.xml';
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// Display results
echo "Running Zaa Radio Test Suite\n";
echo "============================\n\n";

foreach ($output as $line) {
    echo $line . "\n";
}

echo "\nTest execution completed with exit code: $returnCode\n";

if ($returnCode === 0) {
    echo "All tests passed! ✅\n";
} else {
    echo "Some tests failed! ❌\n";
}

exit($returnCode);

