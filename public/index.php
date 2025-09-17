<?php
/**
 * Zaa Radio Booking System - Entry Point
 * Simple router for handling requests
 */

// Start session
session_start();

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Simple router
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove query string and trailing slash
$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

// Route handling
switch ($path) {
    case '/':
        include __DIR__ . '/views/landing.php';
        break;
        
    case '/book':
        include __DIR__ . '/views/booking.php';
        break;
        
    case '/admin':
        include __DIR__ . '/views/admin/dashboard.php';
        break;
        
    case '/manager':
        include __DIR__ . '/views/manager/dashboard.php';
        break;
        
    case '/advertiser':
        include __DIR__ . '/views/advertiser/dashboard.php';
        break;
        
    case '/login':
        include __DIR__ . '/views/auth/login.php';
        break;
        
    case '/logout':
        session_destroy();
        header('Location: /');
        exit;
        break;
        
    case '/api/slots':
        header('Content-Type: application/json');
        include __DIR__ . '/api/slots.php';
        break;
        
    default:
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        break;
}
?>