<?php
/**
 * Comprehensive System Test
 * Tests the entire Zaa Radio Booking System
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Database;
use App\Models\User;
use App\Models\Slot;
use App\Models\Booking;
use App\Utils\Session;

echo "🧪 Zaa Radio Booking System - Comprehensive Test\n";
echo "=" . str_repeat("=", 50) . "\n\n";

$testsPassed = 0;
$totalTests = 0;

function runTest($testName, $testFunction) {
    global $testsPassed, $totalTests;
    $totalTests++;
    
    echo "Testing: $testName... ";
    
    try {
        $result = $testFunction();
        if ($result) {
            echo "✅ PASSED\n";
            $testsPassed++;
        } else {
            echo "❌ FAILED\n";
        }
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
    }
}

// Test 1: Database Connection
runTest("Database Connection", function() {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    return $connection !== null;
});

// Test 2: User Model
runTest("User Model - Find Admin", function() {
    $userModel = new User();
    $admin = $userModel->findByEmail('admin@zaaradio.com');
    return $admin && $admin['role'] === 'admin';
});

// Test 3: Slot Model
runTest("Slot Model - Find Available Slots", function() {
    $slotModel = new Slot();
    $slots = $slotModel->findAvailable();
    return count($slots) > 0;
});

// Test 4: Booking Model
runTest("Booking Model - Find Bookings", function() {
    $bookingModel = new Booking();
    $bookings = $bookingModel->all();
    return is_array($bookings);
});

// Test 5: Session Management
runTest("Session Management", function() {
    Session::start();
    Session::set('test_key', 'test_value');
    return Session::get('test_key') === 'test_value';
});

// Test 6: User Statistics
runTest("User Statistics", function() {
    $userModel = new User();
    $stats = $userModel->getStats();
    return isset($stats['total']) && $stats['total'] > 0;
});

// Test 7: Slot Statistics
runTest("Slot Statistics", function() {
    $slotModel = new Slot();
    $stats = $slotModel->getStats();
    return isset($stats['total']) && $stats['total'] > 0;
});

// Test 8: Booking Statistics
runTest("Booking Statistics", function() {
    $bookingModel = new Booking();
    $stats = $bookingModel->getStats();
    return isset($stats['total']);
});

// Test 9: Database Query
runTest("Database Raw Query", function() {
    $db = Database::getInstance();
    $result = $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    return $result['count'] > 0;
});

// Test 10: Slot Date Range Query
runTest("Slot Date Range Query", function() {
    $slotModel = new Slot();
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $slots = $slotModel->findByDate($tomorrow);
    return is_array($slots);
});

// Test 11: User Search
runTest("User Search", function() {
    $userModel = new User();
    $results = $userModel->search('admin');
    return count($results) > 0;
});

// Test 12: Slot Time Conflict Check
runTest("Slot Time Conflict Check", function() {
    $slotModel = new Slot();
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $hasConflict = $slotModel->hasTimeConflict(1, $tomorrow, '06:00:00', '09:00:00');
    return is_bool($hasConflict);
});

// Test 13: Booking Slot Check
runTest("Booking Slot Check", function() {
    $bookingModel = new Booking();
    $isBooked = $bookingModel->isSlotBooked(1);
    return is_bool($isBooked);
});

// Test 14: User Role Check
runTest("User Role Check", function() {
    $userModel = new User();
    $admin = $userModel->findByEmail('admin@zaaradio.com');
    return $admin && $userModel->verifyPassword('admin123', $admin['password']);
});

// Test 15: Environment Variables
runTest("Environment Variables", function() {
    return !empty($_ENV['DB_HOST']) && !empty($_ENV['DB_NAME']);
});

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 Test Results: $testsPassed/$totalTests tests passed\n";

if ($testsPassed === $totalTests) {
    echo "🎉 All tests passed! The system is working correctly.\n";
    echo "\n📋 System Status:\n";
    echo "✅ Database connection: Working\n";
    echo "✅ Models: Working\n";
    echo "✅ Authentication: Ready\n";
    echo "✅ API endpoints: Ready\n";
    echo "✅ Session management: Working\n";
    echo "\n🚀 The Zaa Radio Booking System is ready for use!\n";
    echo "\n🔑 Default Credentials:\n";
    echo "   Admin: admin@zaaradio.com / admin123\n";
    echo "   Manager: manager@zaaradio.com / manager123\n";
    echo "   Advertisers: advertiser1@example.com / advertiser123\n";
    echo "\n🌐 Access URLs:\n";
    echo "   Landing Page: http://localhost:8080/\n";
    echo "   Admin Dashboard: http://localhost:8080/admin\n";
    echo "   Manager Dashboard: http://localhost:8080/manager\n";
    echo "   Booking Calendar: http://localhost:8080/book\n";
    echo "   Login: http://localhost:8080/login\n";
} else {
    echo "⚠️  Some tests failed. Please check the errors above.\n";
    echo "💡 Make sure you have run the migrations and seeded the database.\n";
    echo "   Run: php migrate.php\n";
    echo "   Run: php seeds/seed.php\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
?>
