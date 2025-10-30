<?php
/**
 * Test Campaign Booking - Diagnostic Tool
 * Tests the booking flow with sample data
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\BookingController;
use App\Utils\Session;

// Start session
Session::start();

// Set a test CSRF token
$_SESSION['csrf_token'] = 'test_token_12345';

header('Content-Type: application/json');

// Create test payload
$testPayload = [
    'csrf_token' => 'test_token_12345',
    'ad_type' => 'lpm',
    'selected_slots' => [
        [
            'date' => '2025-10-28',
            'start_time' => '20:30:00',
            'end_time' => '21:30:00',
            'day_of_week' => 'tuesday'
        ],
        [
            'date' => '2025-10-29',
            'start_time' => '20:30:00',
            'end_time' => '21:30:00',
            'day_of_week' => 'wednesday'
        ]
    ],
    'advertiser_name' => 'Test User',
    'advertiser_email' => 'test' . time() . '@example.com', // Unique email
    'advertiser_phone' => '0242880401',
    'company_name' => 'Test Company',
    'message' => 'Test booking message',
    'start_date' => '2025-10-28',
    'end_date' => '2025-10-29',
    'recurrence' => 'daily',
    'weekdays' => []
];

// Simulate POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest'; // Make it an AJAX request

// Put data in php://input simulation
$GLOBALS['test_input'] = json_encode($testPayload);

// Override file_get_contents for php://input
stream_wrapper_unregister('php');
stream_wrapper_register('php', TestStreamWrapper::class);

// Create controller and test
try {
    $controller = new BookingController();
    $controller->confirmCampaignBooking();
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

// Stream wrapper for testing
class TestStreamWrapper {
    private $position;
    private $content;
    
    public function stream_open($path, $mode, $options, &$opened_path) {
        if ($path === 'php://input') {
            $this->content = $GLOBALS['test_input'];
            $this->position = 0;
            return true;
        }
        return false;
    }
    
    public function stream_read($count) {
        $ret = substr($this->content, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    
    public function stream_eof() {
        return $this->position >= strlen($this->content);
    }
    
    public function stream_stat() {
        return [];
    }
}


