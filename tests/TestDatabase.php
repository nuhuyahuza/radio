<?php

namespace Tests;

use App\Database\Database;

/**
 * Test Database Helper
 * Manages test database setup and teardown
 */
class TestDatabase
{
    private static $instance;
    private $db;

    private function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set up test database
     */
    public function setUp()
    {
        // Create test database if it doesn't exist
        $this->createTestDatabase();
        
        // Run migrations
        $this->runMigrations();
        
        // Seed test data
        $this->seedTestData();
    }

    /**
     * Tear down test database
     */
    public function tearDown()
    {
        // Clean up test data
        $this->cleanupTestData();
    }

    /**
     * Create test database
     */
    private function createTestDatabase()
    {
        $dbName = $_ENV['DB_DATABASE'] ?? 'zaa_radio_test';
        
        try {
            $this->db->query("CREATE DATABASE IF NOT EXISTS `$dbName`");
            $this->db->query("USE `$dbName`");
        } catch (\Exception $e) {
            throw new \Exception("Failed to create test database: " . $e->getMessage());
        }
    }

    /**
     * Run database migrations
     */
    private function runMigrations()
    {
        $migrationFiles = glob(__DIR__ . '/../migrations/*.sql');
        
        foreach ($migrationFiles as $file) {
            $sql = file_get_contents($file);
            $statements = explode(';', $sql);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $this->db->query($statement);
                }
            }
        }
    }

    /**
     * Seed test data
     */
    private function seedTestData()
    {
        // Create test users
        $this->createTestUsers();
        
        // Create test slots
        $this->createTestSlots();
        
        // Create test bookings
        $this->createTestBookings();
    }

    /**
     * Create test users
     */
    private function createTestUsers()
    {
        $users = [
            [
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Test Manager',
                'email' => 'manager@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'manager',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Test Advertiser',
                'email' => 'advertiser@test.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'advertiser',
                'is_active' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($users as $user) {
            $this->db->query(
                "INSERT INTO users (name, email, password, role, is_active, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($user)
            );
        }
    }

    /**
     * Create test slots
     */
    private function createTestSlots()
    {
        $slots = [
            [
                'date' => date('Y-m-d', strtotime('+1 day')),
                'start_time' => '06:00:00',
                'end_time' => '09:00:00',
                'status' => 'available',
                'price' => 100.00,
                'description' => 'Morning Drive Slot',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'date' => date('Y-m-d', strtotime('+2 days')),
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'status' => 'available',
                'price' => 75.00,
                'description' => 'Midday Slot',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'date' => date('Y-m-d', strtotime('+3 days')),
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'status' => 'booked',
                'price' => 150.00,
                'description' => 'Evening Rush Slot',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($slots as $slot) {
            $this->db->query(
                "INSERT INTO slots (date, start_time, end_time, status, price, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($slot)
            );
        }
    }

    /**
     * Create test bookings
     */
    private function createTestBookings()
    {
        $bookings = [
            [
                'slot_id' => 1,
                'advertiser_id' => 3,
                'advertisement_message' => 'Test advertisement message',
                'status' => 'pending',
                'total_amount' => 100.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'slot_id' => 2,
                'advertiser_id' => 3,
                'advertisement_message' => 'Another test advertisement',
                'status' => 'approved',
                'total_amount' => 75.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($bookings as $booking) {
            $this->db->query(
                "INSERT INTO bookings (slot_id, advertiser_id, advertisement_message, status, total_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                array_values($booking)
            );
        }
    }

    /**
     * Clean up test data
     */
    private function cleanupTestData()
    {
        $tables = ['bookings', 'slots', 'users', 'audit_logs', 'notifications', 'password_resets'];
        
        foreach ($tables as $table) {
            $this->db->query("DELETE FROM $table");
        }
    }

    /**
     * Get database instance
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Reset auto increment counters
     */
    public function resetAutoIncrement()
    {
        $tables = ['users', 'slots', 'bookings', 'audit_logs', 'notifications', 'password_resets'];
        
        foreach ($tables as $table) {
            $this->db->query("ALTER TABLE $table AUTO_INCREMENT = 1");
        }
    }
}

