<?php
/**
 * Zaa Radio Booking System - Entry Point
 * Simple router for handling requests
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\Session;
use App\Utils\SecurityInitializer;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

// Start session early so all later checks (including CSRF) have access
Session::start();

// Initialize security measures
SecurityInitializer::initialize();

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
        $bookingController = new \App\Controllers\BookingController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingController->createBooking();
        } else {
            $bookingController->showBookingCalendar();
        }
        break;
        
    case '/admin':
        $adminController = new \App\Controllers\AdminDashboardController();
        $adminController->showDashboard();
        break;
        
    case '/manager':
        $managerController = new \App\Controllers\ManagerDashboardController();
        $managerController->showDashboard();
        break;
        
    case '/advertiser':
        $advertiserController = new \App\Controllers\AdvertiserDashboardController();
        $advertiserController->showDashboard();
        break;
        
    case '/login':
        $authController = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLogin();
        }
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
        
    case '/api/csrf-token':
        header('Content-Type: application/json');
        include __DIR__ . '/api/csrf-token.php';
        break;
        
    case (preg_match('/^\/booking-summary\/(\d+)$/', $path, $matches) ? true : false):
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->showBookingSummary($matches[1]);
        break;
        
    case '/booking-success':
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->showBookingSuccess();
        break;
        
    case (preg_match('/^\/booking\/(\d+)\/confirm$/', $path, $matches) ? true : false):
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->confirmBooking($matches[1]);
        break;
        
    case '/booking-summary':
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->showDraftSummary();
        break;

    case '/booking/confirm':
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->confirmDraft();
        break;

    case '/booking/cancel':
        $bookingController = new \App\Controllers\BookingController();
        $bookingController->cancelDraft();
        break;
        
    case '/manager/slots':
        $slotController = new \App\Controllers\SlotController();
        $slotController->showSlotsManagement();
        break;
        
    case '/manager/slots/create':
        $slotController = new \App\Controllers\SlotController();
        $slotController->showCreateSlot();
        break;
        
    case (preg_match('/^\/manager\/slots\/edit\/(\d+)$/', $path, $matches) ? true : false):
        $slotController = new \App\Controllers\SlotController();
        $slotController->showEditSlot($matches[1]);
        break;
        
    case '/manager/slots/data':
        $slotController = new \App\Controllers\SlotController();
        $slotController->getSlotsData();
        break;
        
    case '/manager/bookings':
        $bookingManagementController = new \App\Controllers\BookingManagementController();
        $bookingManagementController->showBookingsManagement();
        break;
        
    case (preg_match('/^\/manager\/bookings\/(\d+)$/', $path, $matches) ? true : false):
        $bookingManagementController = new \App\Controllers\BookingManagementController();
        $bookingManagementController->showBookingDetails($matches[1]);
        break;
        
    case '/manager/bookings/data':
        $bookingManagementController = new \App\Controllers\BookingManagementController();
        $bookingManagementController->getBookingsData();
        break;
        
    case '/admin/users':
        $userManagementController = new \App\Controllers\UserManagementController();
        $userManagementController->showUserManagement();
        break;
        
    case (preg_match('/^\/admin\/users\/(\d+)$/', $path, $matches) ? true : false):
        $userManagementController = new \App\Controllers\UserManagementController();
        $userManagementController->showUserDetails($matches[1]);
        break;
        
    case '/admin/users/create':
        $userManagementController = new \App\Controllers\UserManagementController();
        $userManagementController->showCreateUser();
        break;
        
    case (preg_match('/^\/admin\/users\/edit\/(\d+)$/', $path, $matches) ? true : false):
        $userManagementController = new \App\Controllers\UserManagementController();
        $userManagementController->showEditUser($matches[1]);
        break;
        
    case '/admin/users/data':
        $userManagementController = new \App\Controllers\UserManagementController();
        $userManagementController->getUsersData();
        break;
        
    case '/admin/reports':
        $reportsController = new \App\Controllers\ReportsController();
        $reportsController->showReports();
        break;
        
    case '/admin/reports/booking-analytics':
        $reportsController = new \App\Controllers\ReportsController();
        $reportsController->getBookingAnalytics();
        break;
        
    case '/admin/reports/revenue-analytics':
        $reportsController = new \App\Controllers\ReportsController();
        $reportsController->getRevenueAnalytics();
        break;
        
    case '/admin/reports/user-analytics':
        $reportsController = new \App\Controllers\ReportsController();
        $reportsController->getUserAnalytics();
        break;
        
    case '/admin/reports/export':
        $reportsController = new \App\Controllers\ReportsController();
        $reportsController->exportReports();
        break;
        
    case '/admin/bookings':
        $bookingManagementController = new \App\Controllers\BookingManagementController();
        $bookingManagementController->showBookings();
        break;
        
    case (preg_match('/^\/admin\/bookings\/(\d+)$/', $path, $matches) ? true : false):
        $bookingManagementController = new \App\Controllers\BookingManagementController();
        $bookingManagementController->showBookingDetails($matches[1]);
        break;
        
    case '/admin/slots':
        $slotController = new \App\Controllers\SlotController();
        $slotController->showSlots();
        break;
        
    case '/admin/settings':
        $adminController = new \App\Controllers\AdminDashboardController();
        $adminController->showSettings();
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
                    
                    
                case (preg_match('/^\/booking-confirm\/(\d+)$/', $path, $matches) ? true : false):
                    $bookingController = new \App\Controllers\BookingController();
                    $bookingController->confirmBooking($matches[1]);
                    break;
                    
                case '/manager/slots/create':
                    $slotController = new \App\Controllers\SlotController();
                    $slotController->createSlot();
                    break;
                    
                case (preg_match('/^\/manager\/slots\/edit\/(\d+)$/', $path, $matches) ? true : false):
                    $slotController = new \App\Controllers\SlotController();
                    $slotController->updateSlot($matches[1]);
                    break;
                    
                case (preg_match('/^\/manager\/slots\/delete\/(\d+)$/', $path, $matches) ? true : false):
                    $slotController = new \App\Controllers\SlotController();
                    $slotController->deleteSlot($matches[1]);
                    break;
                    
                    case (preg_match('/^\/manager\/slots\/cancel\/(\d+)$/', $path, $matches) ? true : false):
                        $slotController = new \App\Controllers\SlotController();
                        $slotController->cancelSlot($matches[1]);
                        break;
                        
                    case (preg_match('/^\/manager\/bookings\/approve\/(\d+)$/', $path, $matches) ? true : false):
                        $bookingManagementController = new \App\Controllers\BookingManagementController();
                        $bookingManagementController->approveBooking($matches[1]);
                        break;
                        
                    case (preg_match('/^\/manager\/bookings\/reject\/(\d+)$/', $path, $matches) ? true : false):
                        $bookingManagementController = new \App\Controllers\BookingManagementController();
                        $bookingManagementController->rejectBooking($matches[1]);
                        break;
                        
                    case '/admin/users/create':
                        $userManagementController = new \App\Controllers\UserManagementController();
                        $userManagementController->createUser();
                        break;
                        
                    case (preg_match('/^\/admin\/users\/edit\/(\d+)$/', $path, $matches) ? true : false):
                        $userManagementController = new \App\Controllers\UserManagementController();
                        $userManagementController->updateUser($matches[1]);
                        break;
                        
                    case (preg_match('/^\/admin\/users\/delete\/(\d+)$/', $path, $matches) ? true : false):
                        $userManagementController = new \App\Controllers\UserManagementController();
                        $userManagementController->deleteUser($matches[1]);
                        break;
                        
                    case (preg_match('/^\/admin\/users\/toggle-status\/(\d+)$/', $path, $matches) ? true : false):
                        $userManagementController = new \App\Controllers\UserManagementController();
                        $userManagementController->toggleUserStatus($matches[1]);
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