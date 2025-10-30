<?php
/**
 * Database Migration Status Checker
 * Check if campaign booking tables are properly set up
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance();
    
    echo "<!DOCTYPE html><html><head><title>Migration Status</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .sql-box { background: #f8f8f8; padding: 15px; border-left: 4px solid #667eea; margin: 20px 0; overflow-x: auto; }
        pre { margin: 0; white-space: pre-wrap; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
    </style></head><body><div class='container'>";
    
    echo "<h1>🔍 Database Migration Status Check</h1>";
    
    // Check bookings table structure
    echo "<h2>1. Checking 'bookings' table...</h2>";
    
    $bookingsColumns = $db->fetchAll("SHOW COLUMNS FROM bookings");
    $hasAdType = false;
    $hasSlotIdNullable = false;
    $hasCampaignStart = false;
    
    echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($bookingsColumns as $col) {
        if ($col['Field'] === 'ad_type') $hasAdType = true;
        if ($col['Field'] === 'campaign_start') $hasCampaignStart = true;
        if ($col['Field'] === 'slot_id' && $col['Null'] === 'YES') $hasSlotIdNullable = true;
        
        echo "<tr><td>" . htmlspecialchars($col['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h3>Required Columns Status:</h3>";
    echo "<p>✓ slot_id nullable: " . ($hasSlotIdNullable ? "<span class='success'>YES ✓</span>" : "<span class='error'>NO ✗ (MUST FIX)</span>") . "</p>";
    echo "<p>✓ ad_type exists: " . ($hasAdType ? "<span class='success'>YES ✓</span>" : "<span class='error'>NO ✗ (MUST ADD)</span>") . "</p>";
    echo "<p>✓ campaign_start exists: " . ($hasCampaignStart ? "<span class='success'>YES ✓</span>" : "<span class='error'>NO ✗ (MUST ADD)</span>") . "</p>";
    
    // Check booking_sessions table
    echo "<h2>2. Checking 'booking_sessions' table...</h2>";
    
    try {
        $sessionsColumns = $db->fetchAll("SHOW COLUMNS FROM booking_sessions");
        echo "<span class='success'>✓ Table EXISTS</span><br><br>";
        echo "<table><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($sessionsColumns as $col) {
            echo "<tr><td>" . htmlspecialchars($col['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Key']) . "</td></tr>";
        }
        echo "</table>";
        $hasSessionsTable = true;
    } catch (Exception $e) {
        echo "<span class='error'>✗ Table DOES NOT EXIST (MUST CREATE)</span><br>";
        echo "<p class='warning'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        $hasSessionsTable = false;
    }
    
    // Overall status
    echo "<hr><h2>📊 Overall Migration Status</h2>";
    
    if ($hasSlotIdNullable && $hasAdType && $hasCampaignStart && $hasSessionsTable) {
        echo "<p class='success' style='font-size: 1.2em;'>✓✓✓ ALL MIGRATIONS COMPLETED! System is ready to use. ✓✓✓</p>";
        echo "<p><a href='/book' class='btn'>Go to Booking Page →</a></p>";
    } else {
        echo "<p class='error' style='font-size: 1.2em;'>✗ MIGRATION REQUIRED</p>";
        echo "<p>You need to run the database migration to use campaign bookings.</p>";
        
        echo "<div class='sql-box'>";
        echo "<h3>Run this SQL in phpMyAdmin:</h3>";
        echo "<pre>";
        
        if (!$hasSlotIdNullable) {
            echo "-- Step 1: Make slot_id nullable\n";
            echo "ALTER TABLE bookings MODIFY COLUMN slot_id INT DEFAULT NULL;\n\n";
        }
        
        if (!$hasAdType || !$hasCampaignStart) {
            echo "-- Step 2: Add campaign columns\n";
            if (!$hasAdType) echo "ALTER TABLE bookings ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') DEFAULT NULL;\n";
            echo "ALTER TABLE bookings ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL;\n";
            if (!$hasCampaignStart) echo "ALTER TABLE bookings ADD COLUMN campaign_start DATE DEFAULT NULL;\n";
            echo "ALTER TABLE bookings ADD COLUMN campaign_end DATE DEFAULT NULL;\n";
            echo "ALTER TABLE bookings ADD COLUMN frequency_per_day INT DEFAULT NULL;\n";
            echo "ALTER TABLE bookings ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL;\n\n";
        }
        
        if (!$hasSessionsTable) {
            echo "-- Step 3: Create booking_sessions table\n";
            echo "CREATE TABLE booking_sessions (\n";
            echo "  id INT AUTO_INCREMENT PRIMARY KEY,\n";
            echo "  booking_id INT NOT NULL,\n";
            echo "  session_date DATE NOT NULL,\n";
            echo "  start_time TIME NOT NULL,\n";
            echo "  end_time TIME NOT NULL,\n";
            echo "  status ENUM('pending','approved','cancelled','completed') NOT NULL DEFAULT 'pending',\n";
            echo "  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,\n";
            echo "  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
            echo "  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,\n";
            echo "  INDEX idx_booking_session (booking_id, session_date),\n";
            echo "  INDEX idx_session_date (session_date, start_time)\n";
            echo ");\n";
        }
        
        echo "</pre>";
        echo "</div>";
        
        echo "<h3>Instructions:</h3>";
        echo "<ol>";
        echo "<li>Open <strong>phpMyAdmin</strong> at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Select the <strong>zaa_radio</strong> database</li>";
        echo "<li>Click the <strong>SQL</strong> tab</li>";
        echo "<li><strong>Copy and paste</strong> the SQL above</li>";
        echo "<li>Click <strong>Go</strong></li>";
        echo "<li><strong>Refresh this page</strong> to verify</li>";
        echo "</ol>";
    }
    
    echo "</div></body></html>";
    
} catch (Exception $e) {
    echo "<div class='container'>";
    echo "<h1 class='error'>❌ Database Connection Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure your database is running and configuration is correct.</p>";
    echo "</div></body></html>";
}


