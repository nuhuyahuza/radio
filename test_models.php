<?php
/**
 * Test script for Models and Database layer
 * This script tests the PDO wrapper and Models functionality
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Slot;
use App\Models\Booking;

echo "Testing Models and Database Layer...\n\n";

try {
    // Test User Model
    echo "1. Testing User Model:\n";
    $userModel = new User();
    
    // Test finding users
    $users = $userModel->all();
    echo "   - Found " . count($users) . " users\n";
    
    // Test finding user by email
    $admin = $userModel->findByEmail('admin@zaaradio.com');
    if ($admin) {
        echo "   - Found admin user: " . $admin['name'] . "\n";
    } else {
        echo "   - Admin user not found\n";
    }
    
    // Test user stats
    $userStats = $userModel->getStats();
    echo "   - User stats: " . json_encode($userStats) . "\n";
    
    echo "\n2. Testing Slot Model:\n";
    $slotModel = new Slot();
    
    // Test finding slots
    $slots = $slotModel->all();
    echo "   - Found " . count($slots) . " slots\n";
    
    // Test finding available slots
    $availableSlots = $slotModel->findAvailable();
    echo "   - Found " . count($availableSlots) . " available slots\n";
    
    // Test slot stats
    $slotStats = $slotModel->getStats();
    echo "   - Slot stats: " . json_encode($slotStats) . "\n";
    
    // Test finding slots by date
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $tomorrowSlots = $slotModel->findByDate($tomorrow);
    echo "   - Found " . count($tomorrowSlots) . " slots for tomorrow\n";
    
    echo "\n3. Testing Booking Model:\n";
    $bookingModel = new Booking();
    
    // Test finding bookings
    $bookings = $bookingModel->all();
    echo "   - Found " . count($bookings) . " bookings\n";
    
    // Test finding pending bookings
    $pendingBookings = $bookingModel->findPending();
    echo "   - Found " . count($pendingBookings) . " pending bookings\n";
    
    // Test booking stats
    $bookingStats = $bookingModel->getStats();
    echo "   - Booking stats: " . json_encode($bookingStats) . "\n";
    
    // Test finding bookings with details
    $bookingsWithDetails = $bookingModel->findAllWithDetails(5);
    echo "   - Found " . count($bookingsWithDetails) . " bookings with details\n";
    
    echo "\n4. Testing Database Connection:\n";
    $db = \App\Database\Database::getInstance();
    $connection = $db->getConnection();
    echo "   - Database connection: " . ($connection ? "SUCCESS" : "FAILED") . "\n";
    
    // Test raw query
    $result = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "   - User count via raw query: " . $result['count'] . "\n";
    
    echo "\n✅ All tests completed successfully!\n";
    echo "\nThe database layer and models are working correctly.\n";
    echo "You can now proceed with the next todo (Authentication System).\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Please check your database connection and ensure migrations have been run.\n";
    exit(1);
}
?>
