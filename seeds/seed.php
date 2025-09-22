<?php
/**
 * Database Seeder
 * Populates the database with sample data for development and testing
 */

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

// Database configuration
$host = $_ENV['DB_HOST'] ?? 'db';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbname = $_ENV['DB_NAME'] ?? 'zaa_radio';
$username = $_ENV['DB_USER'] ?? 'zaa_radio';
$password = $_ENV['DB_PASSWORD'] ?? 'zaa_radio_password';

try {
    // Connect to database
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to database successfully.\n";

    // Clear existing data (in reverse order of dependencies)
    echo "Clearing existing data...\n";
    $tables = ['notifications', 'audit_logs', 'password_resets', 'bookings', 'slots', 'stations', 'users'];
    foreach ($tables as $table) {
        try {
            $pdo->exec("DELETE FROM $table");
        } catch (PDOException $e) {
            // Table might not exist, continue
            echo "Note: Table $table not found or already empty\n";
        }
    }

    // Reset auto-increment counters
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE stations AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE slots AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE bookings AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE audit_logs AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE notifications AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE password_resets AUTO_INCREMENT = 1");

    echo "✓ Existing data cleared\n";

    // 1. Create Station
    echo "Creating station...\n";
    $stmt = $pdo->prepare("
        INSERT INTO stations (name, description, timezone, is_active) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        'Zaa Radio',
        'Premier radio station serving the community with quality programming and advertising opportunities.',
        'UTC',
        true
    ]);
    $stationId = $pdo->lastInsertId();
    echo "✓ Station created (ID: $stationId)\n";

    // 2. Create Users
    echo "Creating users...\n";
    
    // Admin user
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, phone, company, is_active, email_verified_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'System Administrator',
        'admin@zaaradio.com',
        $adminPassword,
        'admin',
        '+1-555-0100',
        'Zaa Radio',
        true,
        date('Y-m-d H:i:s')
    ]);
    $adminId = $pdo->lastInsertId();
    echo "✓ Admin user created (ID: $adminId)\n";

    // Station Manager
    $managerPassword = password_hash('manager123', PASSWORD_DEFAULT);
    $stmt->execute([
        'John Manager',
        'manager@zaaradio.com',
        $managerPassword,
        'station_manager',
        '+1-555-0101',
        'Zaa Radio',
        true,
        date('Y-m-d H:i:s')
    ]);
    $managerId = $pdo->lastInsertId();
    echo "✓ Station Manager created (ID: $managerId)\n";

    // Sample Advertisers
    $advertisers = [
        ['ABC Company', 'advertiser1@example.com', '+1-555-0201', 'ABC Company'],
        ['XYZ Corporation', 'advertiser2@example.com', '+1-555-0202', 'XYZ Corporation'],
        ['Local Business', 'advertiser3@example.com', '+1-555-0203', 'Local Business Inc'],
        ['Tech Startup', 'advertiser4@example.com', '+1-555-0204', 'Tech Startup LLC'],
        ['Retail Store', 'advertiser5@example.com', '+1-555-0205', 'Retail Store Chain']
    ];

    $advertiserIds = [];
    foreach ($advertisers as $index => $advertiser) {
        $advertiserPassword = password_hash('advertiser123', PASSWORD_DEFAULT);
        $stmt->execute([
            $advertiser[0],
            $advertiser[1],
            $advertiserPassword,
            'advertiser',
            $advertiser[2],
            $advertiser[3],
            true,
            date('Y-m-d H:i:s')
        ]);
        $advertiserIds[] = $pdo->lastInsertId();
    }
    echo "✓ " . count($advertisers) . " Advertisers created\n";

    // 3. Create Slots
    echo "Creating slots...\n";
    
    // Generate slots for the next 30 days
    $slotCount = 0;
    $slotTypes = [
        ['Morning Drive', '06:00:00', '09:00:00', 150.00],
        ['Midday', '12:00:00', '14:00:00', 100.00],
        ['Evening Rush', '17:00:00', '19:00:00', 200.00],
        ['Late Night', '22:00:00', '24:00:00', 75.00]
    ];

    for ($day = 0; $day < 30; $day++) {
        $date = date('Y-m-d', strtotime("+$day days"));
        
        foreach ($slotTypes as $slotType) {
            // Randomly make some slots booked or cancelled
            $statuses = ['available', 'available', 'available', 'booked', 'cancelled'];
            $status = $statuses[array_rand($statuses)];
            
            $stmt = $pdo->prepare("
                INSERT INTO slots (station_id, date, start_time, end_time, price, status, description, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $stationId,
                $date,
                $slotType[1],
                $slotType[2],
                $slotType[3],
                $status,
                $slotType[0] . ' slot for ' . $date,
                $managerId
            ]);
            $slotCount++;
        }
    }
    echo "✓ $slotCount slots created\n";

    // 4. Create Sample Bookings
    echo "Creating sample bookings...\n";
    
    // Get some booked slots
    $stmt = $pdo->query("SELECT id FROM slots WHERE status = 'booked' LIMIT 10");
    $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $bookingStatuses = ['pending', 'approved', 'rejected'];
    $bookingCount = 0;
    
    foreach ($bookedSlots as $slotId) {
        $advertiserId = $advertiserIds[array_rand($advertiserIds)];
        $status = $bookingStatuses[array_rand($bookingStatuses)];
        $approvedBy = ($status === 'approved') ? $managerId : null;
        $approvedAt = ($status === 'approved') ? date('Y-m-d H:i:s') : null;
        
        // Get slot price
        $stmt = $pdo->prepare("SELECT price FROM slots WHERE id = ?");
        $stmt->execute([$slotId]);
        $slot = $stmt->fetch();
        
        $stmt = $pdo->prepare("
            INSERT INTO bookings (advertiser_id, slot_id, status, message, total_amount, payment_status, approved_by, approved_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $advertiserId,
            $slotId,
            $status,
            'Sample advertisement message for booking #' . ($bookingCount + 1),
            $slot['price'],
            ($status === 'approved') ? 'paid' : 'pending',
            $approvedBy,
            $approvedAt
        ]);
        $bookingCount++;
    }
    echo "✓ $bookingCount bookings created\n";

    // 5. Create Sample Notifications
    echo "Creating sample notifications...\n";
    
    $notifications = [
        ['booking_received', 'New Booking Received', 'You have a new booking request to review.'],
        ['booking_approved', 'Booking Approved', 'Your booking has been approved and is confirmed.'],
        ['booking_rejected', 'Booking Rejected', 'Your booking request has been rejected.'],
        ['payment_reminder', 'Payment Reminder', 'Please complete your payment for the upcoming slot.'],
        ['slot_reminder', 'Upcoming Slot', 'Your advertisement will air tomorrow at the scheduled time.']
    ];
    
    $notificationCount = 0;
    foreach ($advertiserIds as $advertiserId) {
        $notification = $notifications[array_rand($notifications)];
        
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, type, title, message, is_read) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $advertiserId,
            $notification[0],
            $notification[1],
            $notification[2],
            rand(0, 1) // Random read status
        ]);
        $notificationCount++;
    }
    echo "✓ $notificationCount notifications created\n";

    // 6. Create Sample Audit Logs
    echo "Creating sample audit logs...\n";
    
    $auditActions = [
        'user_created', 'user_updated', 'slot_created', 'slot_updated', 
        'booking_created', 'booking_approved', 'booking_rejected'
    ];
    
    $auditCount = 0;
    for ($i = 0; $i < 20; $i++) {
        $action = $auditActions[array_rand($auditActions)];
        $userId = rand(1, count($advertiserIds) + 2); // Include admin and manager
        
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $action,
            'users',
            rand(1, 10),
            '192.168.1.' . rand(1, 254),
            'Mozilla/5.0 (Sample Browser)'
        ]);
        $auditCount++;
    }
    echo "✓ $auditCount audit logs created\n";

    echo "\n🎉 Database seeding completed successfully!\n";
    echo "\nSample credentials:\n";
    echo "Admin: admin@zaaradio.com / admin123\n";
    echo "Manager: manager@zaaradio.com / manager123\n";
    echo "Advertisers: advertiser1@example.com / advertiser123\n";
    echo "              advertiser2@example.com / advertiser123\n";
    echo "              (and so on...)\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>