<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Tests\TestDatabase;

/**
 * User Model Tests
 */
class UserTest extends TestCase
{
    private $userModel;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->userModel = new User();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    /**
     * Test user creation
     */
    public function testCreateUser()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'advertiser',
            'phone' => '1234567890',
            'company' => 'Test Company',
            'is_active' => 1
        ];

        $userId = $this->userModel->createUser($userData);
        
        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);
    }

    /**
     * Test finding user by email
     */
    public function testFindByEmail()
    {
        $user = $this->userModel->findByEmail('admin@test.com');
        
        $this->assertIsArray($user);
        $this->assertEquals('Test Admin', $user['name']);
        $this->assertEquals('admin', $user['role']);
    }

    /**
     * Test finding user by ID
     */
    public function testFindById()
    {
        $user = $this->userModel->findById(1);
        
        $this->assertIsArray($user);
        $this->assertEquals('Test Admin', $user['name']);
        $this->assertEquals('admin@test.com', $user['email']);
    }

    /**
     * Test password verification
     */
    public function testVerifyPassword()
    {
        $password = 'password123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertTrue($this->userModel->verifyPassword($password, $hash));
        $this->assertFalse($this->userModel->verifyPassword('wrongpassword', $hash));
    }

    /**
     * Test password hashing
     */
    public function testHashPassword()
    {
        $password = 'password123';
        $hash = $this->userModel->hashPassword($password);
        
        $this->assertIsString($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
    }

    /**
     * Test finding users by role
     */
    public function testFindByRole()
    {
        $admins = $this->userModel->findByRole('admin');
        
        $this->assertIsArray($admins);
        $this->assertCount(1, $admins);
        $this->assertEquals('admin', $admins[0]['role']);
    }

    /**
     * Test finding active users
     */
    public function testFindActive()
    {
        $activeUsers = $this->userModel->findActive();
        
        $this->assertIsArray($activeUsers);
        $this->assertGreaterThan(0, count($activeUsers));
        
        foreach ($activeUsers as $user) {
            $this->assertEquals(1, $user['is_active']);
        }
    }

    /**
     * Test updating user
     */
    public function testUpdateUser()
    {
        $updateData = [
            'name' => 'Updated Name',
            'phone' => '9876543210'
        ];

        $result = $this->userModel->updateUser(1, $updateData);
        
        $this->assertTrue($result);
        
        $updatedUser = $this->userModel->findById(1);
        $this->assertEquals('Updated Name', $updatedUser['name']);
        $this->assertEquals('9876543210', $updatedUser['phone']);
    }

    /**
     * Test updating password
     */
    public function testUpdatePassword()
    {
        $newPassword = 'newpassword123';
        $result = $this->userModel->updatePassword(1, $newPassword);
        
        $this->assertTrue($result);
        
        $user = $this->userModel->findById(1);
        $this->assertTrue(password_verify($newPassword, $user['password']));
    }

    /**
     * Test updating last login
     */
    public function testUpdateLastLogin()
    {
        $result = $this->userModel->updateLastLogin(1);
        
        $this->assertTrue($result);
        
        $user = $this->userModel->findById(1);
        $this->assertNotNull($user['last_login_at']);
    }

    /**
     * Test user statistics
     */
    public function testGetStats()
    {
        $stats = $this->userModel->getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('admin', $stats);
        $this->assertArrayHasKey('manager', $stats);
        $this->assertArrayHasKey('advertiser', $stats);
        
        $this->assertGreaterThan(0, $stats['total']);
        $this->assertGreaterThan(0, $stats['active']);
    }

    /**
     * Test searching users
     */
    public function testSearch()
    {
        $results = $this->userModel->search('Test');
        
        $this->assertIsArray($results);
        $this->assertGreaterThan(0, count($results));
        
        foreach ($results as $user) {
            $this->assertStringContainsString('Test', $user['name']);
        }
    }

    /**
     * Test searching users by role
     */
    public function testSearchByRole()
    {
        $results = $this->userModel->search('Test', 'admin');
        
        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('admin', $results[0]['role']);
    }

    /**
     * Test email exists check
     */
    public function testEmailExists()
    {
        $this->assertTrue($this->userModel->emailExists('admin@test.com'));
        $this->assertFalse($this->userModel->emailExists('nonexistent@test.com'));
        $this->assertFalse($this->userModel->emailExists('admin@test.com', 1));
    }

    /**
     * Test getting user with bookings count
     */
    public function testFindWithBookingsCount()
    {
        $user = $this->userModel->findWithBookingsCount(3);
        
        $this->assertIsArray($user);
        $this->assertEquals('Test Advertiser', $user['name']);
        $this->assertArrayHasKey('bookings_count', $user);
        $this->assertArrayHasKey('approved_bookings', $user);
        $this->assertArrayHasKey('pending_bookings', $user);
    }

    /**
     * Test getting recent activity
     */
    public function testGetRecentActivity()
    {
        $activity = $this->userModel->getRecentActivity(3, 5);
        
        $this->assertIsArray($activity);
        $this->assertLessThanOrEqual(5, count($activity));
    }

    /**
     * Test counting users by role
     */
    public function testCountUsersByRole()
    {
        $adminCount = $this->userModel->countUsersByRole('admin');
        $managerCount = $this->userModel->countUsersByRole('manager');
        $advertiserCount = $this->userModel->countUsersByRole('advertiser');
        
        $this->assertEquals(1, $adminCount);
        $this->assertEquals(1, $managerCount);
        $this->assertEquals(1, $advertiserCount);
    }

    /**
     * Test counting active users by role
     */
    public function testCountActiveUsersByRole()
    {
        $activeAdminCount = $this->userModel->countActiveUsersByRole('admin');
        $activeManagerCount = $this->userModel->countActiveUsersByRole('manager');
        $activeAdvertiserCount = $this->userModel->countActiveUsersByRole('advertiser');
        
        $this->assertEquals(1, $activeAdminCount);
        $this->assertEquals(1, $activeManagerCount);
        $this->assertEquals(1, $activeAdvertiserCount);
    }

    /**
     * Test getting all users
     */
    public function testGetAllUsers()
    {
        $users = $this->userModel->getAllUsers();
        
        $this->assertIsArray($users);
        $this->assertCount(3, $users);
        
        foreach ($users as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('name', $user);
            $this->assertArrayHasKey('email', $user);
            $this->assertArrayHasKey('role', $user);
        }
    }
}

