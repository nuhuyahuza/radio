<?php
/**
 * Clear Test Bookings - Remove test data to unblock slots
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    
    // Delete test booking sessions
    $deletedSessions = $db->execute("DELETE FROM booking_sessions WHERE booking_id IN (
        SELECT id FROM bookings WHERE advertiser_id IN (
            SELECT id FROM users WHERE email LIKE '%@example.com' OR email LIKE 'test%@test.com'
        )
    )");
    
    // Delete test bookings
    $deletedBookings = $db->execute("DELETE FROM bookings WHERE advertiser_id IN (
        SELECT id FROM users WHERE email LIKE '%@example.com' OR email LIKE 'test%@test.com'
    )");
    
    // Optionally delete test users (uncomment if needed)
    // $deletedUsers = $db->execute("DELETE FROM users WHERE email LIKE '%@example.com' OR email LIKE 'test%@test.com'");
    
    echo json_encode([
        'success' => true,
        'message' => 'Test bookings cleared successfully',
        'deleted' => [
            'sessions' => $deletedSessions,
            'bookings' => $deletedBookings
        ]
    ]);
    
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}


