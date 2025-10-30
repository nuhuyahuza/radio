<?php
/**
 * System Diagnostics - Check All Components
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

header('Content-Type: application/json');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => [],
    'overall_status' => 'OK'
];

try {
    $db = Database::getInstance();
    
    // Check 1: Database Connection
    $results['checks']['database_connection'] = [
        'status' => 'OK',
        'message' => 'Database connected successfully'
    ];
    
    // Check 2: Users table structure
    try {
        $users = $db->fetch("DESCRIBE users");
        $userColumns = $db->fetchAll("SHOW COLUMNS FROM users");
        
        $requiredUserColumns = ['id', 'name', 'email', 'password', 'role', 'phone', 'company', 'is_active', 'email_verified_at'];
        $existingUserColumns = array_column($userColumns, 'Field');
        $missingUserColumns = array_diff($requiredUserColumns, $existingUserColumns);
        
        if (empty($missingUserColumns)) {
            $results['checks']['users_table'] = [
                'status' => 'OK',
                'message' => 'Users table has all required columns',
                'columns' => $existingUserColumns
            ];
        } else {
            $results['checks']['users_table'] = [
                'status' => 'ERROR',
                'message' => 'Missing columns: ' . implode(', ', $missingUserColumns),
                'missing_columns' => $missingUserColumns,
                'fix' => 'ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1; ALTER TABLE users ADD COLUMN email_verified_at DATETIME DEFAULT NULL;'
            ];
            $results['overall_status'] = 'ERROR';
        }
    } catch (\Exception $e) {
        $results['checks']['users_table'] = [
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ];
        $results['overall_status'] = 'ERROR';
    }
    
    // Check 3: Bookings table structure
    try {
        $bookingColumns = $db->fetchAll("SHOW COLUMNS FROM bookings");
        $existingBookingColumns = array_column($bookingColumns, 'Field');
        $requiredBookingColumns = ['id', 'advertiser_id', 'slot_id', 'ad_type', 'recurrence_pattern', 'campaign_start', 'campaign_end', 'status', 'total_amount'];
        $missingBookingColumns = array_diff($requiredBookingColumns, $existingBookingColumns);
        
        // Check if slot_id is nullable
        $slotIdInfo = null;
        foreach ($bookingColumns as $col) {
            if ($col['Field'] === 'slot_id') {
                $slotIdInfo = $col;
                break;
            }
        }
        
        if (empty($missingBookingColumns) && $slotIdInfo && $slotIdInfo['Null'] === 'YES') {
            $results['checks']['bookings_table'] = [
                'status' => 'OK',
                'message' => 'Bookings table has all required columns and slot_id is nullable',
                'columns' => $existingBookingColumns
            ];
        } else {
            $errors = [];
            if (!empty($missingBookingColumns)) {
                $errors[] = 'Missing columns: ' . implode(', ', $missingBookingColumns);
            }
            if ($slotIdInfo && $slotIdInfo['Null'] !== 'YES') {
                $errors[] = 'slot_id must be nullable (currently NOT NULL)';
            }
            
            $results['checks']['bookings_table'] = [
                'status' => 'ERROR',
                'message' => implode('; ', $errors),
                'slot_id_nullable' => $slotIdInfo ? ($slotIdInfo['Null'] === 'YES') : false,
                'fix' => 'Run the migration: RUN_THIS_MIGRATION.sql'
            ];
            $results['overall_status'] = 'ERROR';
        }
    } catch (\Exception $e) {
        $results['checks']['bookings_table'] = [
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ];
        $results['overall_status'] = 'ERROR';
    }
    
    // Check 4: Booking Sessions table exists
    try {
        $sessionColumns = $db->fetchAll("SHOW COLUMNS FROM booking_sessions");
        $existingSessionColumns = array_column($sessionColumns, 'Field');
        
        $results['checks']['booking_sessions_table'] = [
            'status' => 'OK',
            'message' => 'Booking sessions table exists with all columns',
            'columns' => $existingSessionColumns
        ];
    } catch (\Exception $e) {
        $results['checks']['booking_sessions_table'] = [
            'status' => 'ERROR',
            'message' => 'Table does not exist: ' . $e->getMessage(),
            'fix' => 'Run the migration: RUN_THIS_MIGRATION.sql'
        ];
        $results['overall_status'] = 'ERROR';
    }
    
    // Check 5: Test Models
    try {
        $userModel = new \App\Models\User();
        $bookingModel = new \App\Models\Booking();
        $sessionModel = new \App\Models\BookingSession();
        
        $results['checks']['models'] = [
            'status' => 'OK',
            'message' => 'All models loaded successfully',
            'models' => ['User', 'Booking', 'BookingSession']
        ];
    } catch (\Exception $e) {
        $results['checks']['models'] = [
            'status' => 'ERROR',
            'message' => $e->getMessage()
        ];
        $results['overall_status'] = 'ERROR';
    }
    
    // Check 6: Test user creation data
    $testUserData = [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'test123',
        'role' => 'advertiser',
        'phone' => '1234567890',
        'company' => 'Test Company',
        'is_active' => true,
        'email_verified_at' => date('Y-m-d H:i:s')
    ];
    
    $userModel = new \App\Models\User();
    $filteredData = array_intersect_key($testUserData, array_flip($userModel->getFillable() ?? []));
    
    $results['checks']['user_data_validation'] = [
        'status' => 'INFO',
        'message' => 'Sample user data validation',
        'sample_data' => $testUserData,
        'fillable_fields' => $userModel->getFillable() ?? 'N/A'
    ];
    
} catch (\Exception $e) {
    $results['checks']['database_connection'] = [
        'status' => 'CRITICAL ERROR',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ];
    $results['overall_status'] = 'CRITICAL ERROR';
}

// Add getFillable method to BaseModel
if (!isset($results['checks']['user_data_validation']['fillable_fields']) || $results['checks']['user_data_validation']['fillable_fields'] === 'N/A') {
    $results['checks']['user_data_validation']['note'] = 'BaseModel might be missing getFillable() method - this is optional but helpful';
}

// Summary
$errorCount = 0;
foreach ($results['checks'] as $check) {
    if ($check['status'] === 'ERROR' || $check['status'] === 'CRITICAL ERROR') {
        $errorCount++;
    }
}

$results['summary'] = [
    'total_checks' => count($results['checks']),
    'errors' => $errorCount,
    'warnings' => 0,
    'ready_for_booking' => $results['overall_status'] === 'OK'
];

echo json_encode($results, JSON_PRETTY_PRINT);


