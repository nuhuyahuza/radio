<?php

/**
 * Test Bootstrap
 * Sets up the testing environment
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set testing environment
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

// Set up error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock global functions for testing
if (!function_exists('header')) {
    function header($string, $replace = true, $http_response_code = null) {
        // Mock header function for testing
        return true;
    }
}

if (!function_exists('http_response_code')) {
    function http_response_code($code = null) {
        static $currentCode = 200;
        if ($code !== null) {
            $currentCode = $code;
        }
        return $currentCode;
    }
}

if (!function_exists('session_start')) {
    function session_start() {
        // Mock session_start for testing
        return true;
    }
}

if (!function_exists('session_destroy')) {
    function session_destroy() {
        // Mock session_destroy for testing
        return true;
    }
}

if (!function_exists('session_regenerate_id')) {
    function session_regenerate_id($delete_old_session = false) {
        // Mock session_regenerate_id for testing
        return true;
    }
}

if (!function_exists('session_status')) {
    function session_status() {
        // Mock session_status for testing
        return PHP_SESSION_ACTIVE;
    }
}

if (!function_exists('ini_set')) {
    function ini_set($option, $value) {
        // Mock ini_set for testing
        return true;
    }
}

if (!function_exists('error_log')) {
    function error_log($message, $message_type = 0, $destination = null, $extra_headers = null) {
        // Mock error_log for testing
        return true;
    }
}

if (!function_exists('password_hash')) {
    function password_hash($password, $algo, $options = null) {
        // Mock password_hash for testing
        return 'hashed_' . $password;
    }
}

if (!function_exists('password_verify')) {
    function password_verify($password, $hash) {
        // Mock password_verify for testing
        return $hash === 'hashed_' . $password;
    }
}

if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        // Mock random_bytes for testing
        return str_repeat('0', $length);
    }
}

if (!function_exists('bin2hex')) {
    function bin2hex($string) {
        // Mock bin2hex for testing
        return strtoupper(bin2hex($string));
    }
}

if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string) {
        // Mock hash_equals for testing
        return $known_string === $user_string;
    }
}

if (!function_exists('filter_var')) {
    function filter_var($variable, $filter = FILTER_DEFAULT, $options = null) {
        // Mock filter_var for testing
        switch ($filter) {
            case FILTER_VALIDATE_EMAIL:
                return filter_var($variable, FILTER_VALIDATE_EMAIL) !== false ? $variable : false;
            case FILTER_SANITIZE_STRING:
                return strip_tags($variable);
            case FILTER_VALIDATE_INT:
                return filter_var($variable, FILTER_VALIDATE_INT) !== false ? (int)$variable : false;
            default:
                return $variable;
        }
    }
}

if (!function_exists('filter_input')) {
    function filter_input($type, $variable_name, $filter = FILTER_DEFAULT, $options = null) {
        // Mock filter_input for testing
        $value = null;
        
        switch ($type) {
            case INPUT_GET:
                $value = $_GET[$variable_name] ?? null;
                break;
            case INPUT_POST:
                $value = $_POST[$variable_name] ?? null;
                break;
            case INPUT_SERVER:
                $value = $_SERVER[$variable_name] ?? null;
                break;
            case INPUT_ENV:
                $value = $_ENV[$variable_name] ?? null;
                break;
        }
        
        if ($value === null) {
            return null;
        }
        
        return filter_var($value, $filter, $options);
    }
}

// Define constants for testing
if (!defined('INPUT_GET')) {
    define('INPUT_GET', 1);
}
if (!defined('INPUT_POST')) {
    define('INPUT_POST', 2);
}
if (!defined('INPUT_SERVER')) {
    define('INPUT_SERVER', 4);
}
if (!defined('INPUT_ENV')) {
    define('INPUT_ENV', 5);
}
if (!defined('FILTER_VALIDATE_EMAIL')) {
    define('FILTER_VALIDATE_EMAIL', 274);
}
if (!defined('FILTER_SANITIZE_STRING')) {
    define('FILTER_SANITIZE_STRING', 513);
}
if (!defined('FILTER_VALIDATE_INT')) {
    define('FILTER_VALIDATE_INT', 257);
}
if (!defined('FILTER_DEFAULT')) {
    define('FILTER_DEFAULT', 516);
}
if (!defined('PHP_SESSION_ACTIVE')) {
    define('PHP_SESSION_ACTIVE', 2);
}
if (!defined('PHP_SESSION_NONE')) {
    define('PHP_SESSION_NONE', 1);
}
if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', 1);
}
if (!defined('UPLOAD_ERR_OK')) {
    define('UPLOAD_ERR_OK', 0);
}
if (!defined('UPLOAD_ERR_NO_FILE')) {
    define('UPLOAD_ERR_NO_FILE', 4);
}
if (!defined('UPLOAD_ERR_INI_SIZE')) {
    define('UPLOAD_ERR_INI_SIZE', 1);
}
if (!defined('UPLOAD_ERR_FORM_SIZE')) {
    define('UPLOAD_ERR_FORM_SIZE', 2);
}
if (!defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (!defined('JSON_HEX_TAG')) {
    define('JSON_HEX_TAG', 1);
}
if (!defined('JSON_HEX_APOS')) {
    define('JSON_HEX_APOS', 2);
}
if (!defined('JSON_HEX_QUOT')) {
    define('JSON_HEX_QUOT', 4);
}
if (!defined('JSON_HEX_AMP')) {
    define('JSON_HEX_AMP', 8);
}
if (!defined('ENT_QUOTES')) {
    define('ENT_QUOTES', 3);
}
if (!defined('ENT_HTML5')) {
    define('ENT_HTML5', 48);
}
if (!defined('E_ALL')) {
    define('E_ALL', 32767);
}

