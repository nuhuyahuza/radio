<?php
/**
 * Create Test Users Script
 * Creates test users for all roles to verify login functionality
 */

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Database;
use App\Models\User;

try {
    // Initialize database connection
    $db = Database::getInstance();
    $userModel = new User();

    echo "Creating test users...\n";

    // Test users data
    $testUsers = [
        [
            'name' => 'Admin User',
            'email' => 'admin@zaaradio.com',
            'password' => 'admin123',
            'role' => 'admin',
            'phone' => '1234567890',
            'company' => 'Zaa Radio Admin',
            'is_active' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Station Manager',
            'email' => 'manager@zaaradio.com',
            'password' => 'manager123',
            'role' => 'station_manager',
            'phone' => '1234567891',
            'company' => 'Zaa Radio Station',
            'is_active' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Test Advertiser',
            'email' => 'advertiser@zaaradio.com',
            'password' => 'advertiser123',
            'role' => 'advertiser',
            'phone' => '1234567892',
            'company' => 'Test Company',
            'is_active' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]
    ];

    foreach ($testUsers as $userData) {
        // Check if user already exists
        $existingUser = $userModel->findByEmail($userData['email']);
        
        if ($existingUser) {
            echo "User {$userData['email']} already exists. Updating...\n";
            $userModel->update($existingUser['id'], [
                'name' => $userData['name'],
                'password' => $userData['password'], // Will be hashed in updatePassword
                'role' => $userData['role'],
                'phone' => $userData['phone'],
                'company' => $userData['company'],
                'is_active' => $userData['is_active'],
                'email_verified_at' => $userData['email_verified_at']
            ]);
            $userModel->updatePassword($existingUser['id'], $userData['password']);
        } else {
            echo "Creating user {$userData['email']}...\n";
            $userModel->createUser($userData);
        }
    }

    echo "\nTest users created successfully!\n";
    echo "\nLogin credentials:\n";
    echo "Admin: admin@zaaradio.com / admin123\n";
    echo "Manager: manager@zaaradio.com / manager123\n";
    echo "Advertiser: advertiser@zaaradio.com / advertiser123\n";
    echo "\nYou can now test login at: http://localhost/radio/public/login\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>


