<?php
/**
 * Zaa Radio Booking System - Entry Point
 * Simple router for handling requests
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Session;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

// Start session
Session::start();

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
        AuthMiddleware::requireAdmin();
        include __DIR__ . '/views/admin/dashboard.php';
        break;
        
    case '/manager':
        AuthMiddleware::requireManager();
        include __DIR__ . '/views/manager/dashboard.php';
        break;
        
    case '/advertiser':
        AuthMiddleware::requireAdvertiser();
        include __DIR__ . '/views/advertiser/dashboard.php';
        break;
        
    case '/login':
        $authController = new AuthController();
        $authController->showLogin();
        break;
        
    case '/logout':
        $authController = new AuthController();
        $authController->logout();
        break;
        
    case '/register':
        $authController = new AuthController();
        $authController->showRegister();
        break;
        
    case '/forgot-password':
        $authController = new AuthController();
        $authController->showForgotPassword();
        break;
        
    case '/api/slots':
        header('Content-Type: application/json');
        include __DIR__ . '/api/slots.php';
        break;
        
    // Handle POST requests
    default:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submissions
            switch ($path) {
                case '/login':
                    $authController = new AuthController();
                    $authController->login();
                    break;
                    
                case '/register':
                    $authController = new AuthController();
                    $authController->register();
                    break;
                    
                case '/forgot-password':
                    $authController = new AuthController();
                    $authController->forgotPassword();
                    break;
                    
                default:
                    http_response_code(404);
                    include __DIR__ . '/views/404.php';
                    break;
            }
        } else {
            http_response_code(404);
            include __DIR__ . '/views/404.php';
        }
        break;
}
?>