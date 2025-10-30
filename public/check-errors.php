<?php
/**
 * Error Checker - Comprehensive validation
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$errors = [];
$warnings = [];

// Check 1: EmailService has send method
try {
    $emailService = new \App\Utils\Email\EmailService();
    if (!method_exists($emailService, 'send')) {
        $errors[] = 'EmailService missing send() method';
    }
} catch (\Exception $e) {
    $errors[] = 'EmailService error: ' . $e->getMessage();
}

// Check 2: NotificationService can instantiate
try {
    $notificationService = new \App\Utils\NotificationService();
    if (!method_exists($notificationService, 'sendCampaignBookingConfirmation')) {
        $errors[] = 'NotificationService missing sendCampaignBookingConfirmation() method';
    }
} catch (\Exception $e) {
    $errors[] = 'NotificationService error: ' . $e->getMessage();
}

// Check 3: Database connection
try {
    $db = \App\Database\Database::getInstance();
    if (!method_exists($db, 'inTransaction')) {
        $errors[] = 'Database missing inTransaction() method';
    }
} catch (\Exception $e) {
    $errors[] = 'Database error: ' . $e->getMessage();
}

// Check 4: BookingController
try {
    $controller = new \App\Controllers\BookingController();
    if (!method_exists($controller, 'confirmCampaignBooking')) {
        $errors[] = 'BookingController missing confirmCampaignBooking() method';
    }
} catch (\Exception $e) {
    $errors[] = 'BookingController error: ' . $e->getMessage();
}

// Check 5: Check for undefined methods
$reflection = new ReflectionClass(\App\Utils\Email\EmailService::class);
$methods = array_map(function($m) { return $m->getName(); }, $reflection->getMethods());
if (!in_array('send', $methods)) {
    $errors[] = 'EmailService::send() method not found via reflection';
}

echo json_encode([
    'success' => empty($errors),
    'errors' => $errors,
    'warnings' => $warnings,
    'checks' => [
        'EmailService' => class_exists(\App\Utils\Email\EmailService::class),
        'NotificationService' => class_exists(\App\Utils\NotificationService::class),
        'Database' => class_exists(\App\Database\Database::class),
        'BookingController' => class_exists(\App\Controllers\BookingController::class),
    ]
], JSON_PRETTY_PRINT);

