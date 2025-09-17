<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use App\Models\User;
use Tests\TestDatabase;

/**
 * Auth Controller Integration Tests
 */
class AuthControllerTest extends TestCase
{
    private $authController;
    private $userModel;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->authController = new AuthController();
        $this->userModel = new User();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    /**
     * Test successful login
     */
    public function testSuccessfulLogin()
    {
        // Mock POST data
        $_POST = [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Mock user data
        $user = $this->userModel->findByEmail('admin@test.com');
        $this->assertNotNull($user);

        // Test login method
        $this->authController->login();

        // Verify user is logged in
        $this->assertTrue($_SESSION['logged_in'] ?? false);
        $this->assertEquals('admin', $_SESSION['user_role'] ?? null);
    }

    /**
     * Test failed login with wrong password
     */
    public function testFailedLoginWrongPassword()
    {
        // Mock POST data
        $_POST = [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test login method
        $this->authController->login();

        // Verify user is not logged in
        $this->assertFalse($_SESSION['logged_in'] ?? false);
    }

    /**
     * Test failed login with non-existent email
     */
    public function testFailedLoginNonExistentEmail()
    {
        // Mock POST data
        $_POST = [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test login method
        $this->authController->login();

        // Verify user is not logged in
        $this->assertFalse($_SESSION['logged_in'] ?? false);
    }

    /**
     * Test login with invalid CSRF token
     */
    public function testLoginInvalidCsrfToken()
    {
        // Mock POST data
        $_POST = [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'csrf_token' => 'invalid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test login method
        $this->authController->login();

        // Verify user is not logged in
        $this->assertFalse($_SESSION['logged_in'] ?? false);
    }

    /**
     * Test successful registration
     */
    public function testSuccessfulRegistration()
    {
        // Mock POST data
        $_POST = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
            'company' => 'New Company',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test registration method
        $this->authController->register();

        // Verify user was created
        $user = $this->userModel->findByEmail('newuser@test.com');
        $this->assertNotNull($user);
        $this->assertEquals('New User', $user['name']);
        $this->assertEquals('advertiser', $user['role']);
    }

    /**
     * Test registration with existing email
     */
    public function testRegistrationExistingEmail()
    {
        // Mock POST data
        $_POST = [
            'name' => 'Another User',
            'email' => 'admin@test.com', // Already exists
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
            'company' => 'Another Company',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test registration method
        $this->authController->register();

        // Verify user was not created (should fail)
        $users = $this->userModel->getAllUsers();
        $adminUsers = array_filter($users, function($user) {
            return $user['email'] === 'admin@test.com';
        });
        $this->assertCount(1, $adminUsers); // Should still be only 1
    }

    /**
     * Test registration with password mismatch
     */
    public function testRegistrationPasswordMismatch()
    {
        // Mock POST data
        $_POST = [
            'name' => 'New User',
            'email' => 'newuser2@test.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'phone' => '1234567890',
            'company' => 'New Company',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test registration method
        $this->authController->register();

        // Verify user was not created
        $user = $this->userModel->findByEmail('newuser2@test.com');
        $this->assertNull($user);
    }

    /**
     * Test logout
     */
    public function testLogout()
    {
        // Mock logged in user
        $_SESSION = [
            'logged_in' => true,
            'user_id' => 1,
            'user_name' => 'Test User',
            'user_email' => 'test@test.com',
            'user_role' => 'admin'
        ];

        // Test logout method
        $this->authController->logout();

        // Verify session is cleared
        $this->assertEmpty($_SESSION);
    }

    /**
     * Test forgot password with valid email
     */
    public function testForgotPasswordValidEmail()
    {
        // Mock POST data
        $_POST = [
            'email' => 'admin@test.com',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test forgot password method
        $this->authController->forgotPassword();

        // Verify password reset token was created
        $db = $this->testDb->getDb();
        $result = $db->query("SELECT * FROM password_resets WHERE email = 'admin@test.com'");
        $this->assertNotNull($result);
    }

    /**
     * Test forgot password with invalid email
     */
    public function testForgotPasswordInvalidEmail()
    {
        // Mock POST data
        $_POST = [
            'email' => 'nonexistent@test.com',
            'csrf_token' => 'valid_token'
        ];

        // Mock session
        $_SESSION = [
            'csrf_token' => 'valid_token'
        ];

        // Test forgot password method
        $this->authController->forgotPassword();

        // Verify no password reset token was created
        $db = $this->testDb->getDb();
        $result = $db->query("SELECT * FROM password_resets WHERE email = 'nonexistent@test.com'");
        $this->assertNull($result);
    }

    /**
     * Test show login page
     */
    public function testShowLogin()
    {
        // Capture output
        ob_start();
        $this->authController->showLogin();
        $output = ob_get_clean();

        // Verify login form is displayed
        $this->assertStringContainsString('Login', $output);
        $this->assertStringContainsString('email', $output);
        $this->assertStringContainsString('password', $output);
    }

    /**
     * Test show register page
     */
    public function testShowRegister()
    {
        // Capture output
        ob_start();
        $this->authController->showRegister();
        $output = ob_get_clean();

        // Verify registration form is displayed
        $this->assertStringContainsString('Register', $output);
        $this->assertStringContainsString('name', $output);
        $this->assertStringContainsString('email', $output);
        $this->assertStringContainsString('password', $output);
    }

    /**
     * Test show forgot password page
     */
    public function testShowForgotPassword()
    {
        // Capture output
        ob_start();
        $this->authController->showForgotPassword();
        $output = ob_get_clean();

        // Verify forgot password form is displayed
        $this->assertStringContainsString('Forgot Password', $output);
        $this->assertStringContainsString('email', $output);
    }
}

