<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\Session;

/**
 * Authentication Controller
 * Handles login, logout, and authentication-related operations
 */
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        // If already logged in, redirect to appropriate dashboard
        if (Session::isLoggedIn()) {
            $dashboardUrl = \App\Middleware\AuthMiddleware::getDashboardUrl();
            header("Location: $dashboardUrl");
            exit;
        }

        // Generate CSRF token
        $csrfToken = Session::setCsrfToken();

        // Include login view
        include __DIR__ . '/../../public/views/auth/login.php';
    }

    /**
     * Process login form submission
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLogin();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToLogin();
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please enter both email and password.');
            $this->redirectToLogin();
            return;
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            Session::setFlash('error', 'Invalid email or password.');
            $this->redirectToLogin();
            return;
        }

        // Check if user is active
        if (!$user['is_active']) {
            Session::setFlash('error', 'Your account has been deactivated. Please contact support.');
            $this->redirectToLogin();
            return;
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            Session::setFlash('error', 'Invalid email or password.');
            $this->redirectToLogin();
            return;
        }

        // Update last login time
        $this->userModel->updateLastLogin($user['id']);

        // Set user session
        Session::setUser($user);

        // Log activity
        \App\Middleware\AuthMiddleware::logActivity('login', "User logged in from {$_SERVER['REMOTE_ADDR']}");

        // Redirect to intended page or dashboard
        $redirectUrl = Session::getFlash('redirect_after_login');
        if (!$redirectUrl) {
            $redirectUrl = \App\Middleware\AuthMiddleware::getDashboardUrl();
        }

        Session::setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        header("Location: $redirectUrl");
        exit;
    }

    /**
     * Process logout
     */
    public function logout()
    {
        // Log activity
        \App\Middleware\AuthMiddleware::logActivity('logout', "User logged out from {$_SERVER['REMOTE_ADDR']}");

        // Clear session
        Session::logout();

        // Redirect to home page
        Session::setFlash('success', 'You have been logged out successfully.');
        header('Location: /');
        exit;
    }

    /**
     * Show registration form (for advertisers only)
     */
    public function showRegister()
    {
        // Only allow advertiser registration
        $csrfToken = Session::setCsrfToken();
        include __DIR__ . '/../../public/views/auth/register.php';
    }

    /**
     * Process registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToRegister();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToRegister();
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');

        // Validate input
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirectToRegister();
            return;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            Session::setFlash('error', 'An account with this email already exists.');
            $this->redirectToRegister();
            return;
        }

        // Create user
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password, // Will be hashed in createUser method
            'role' => 'advertiser',
            'phone' => $phone,
            'company' => $company,
            'is_active' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userId = $this->userModel->createUser($userData);
            
            // Log activity
            \App\Middleware\AuthMiddleware::logActivity('user_registered', "New advertiser registered: $email");

            Session::setFlash('success', 'Account created successfully! You can now log in.');
            header('Location: /login');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Registration failed. Please try again.');
            $this->redirectToRegister();
            return;
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        $csrfToken = Session::setCsrfToken();
        include __DIR__ . '/../../public/views/auth/forgot-password.php';
    }

    /**
     * Process forgot password
     */
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToForgotPassword();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToForgotPassword();
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address.');
            $this->redirectToForgotPassword();
            return;
        }

        // Check if user exists
        $user = $this->userModel->findByEmail($email);
        
        if ($user) {
            // Generate reset token (in a real app, you'd store this in password_resets table)
            $resetToken = bin2hex(random_bytes(32));
            
            // For demo purposes, we'll just show a success message
            // In production, you'd send an email with the reset link
            Session::setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
        } else {
            // Don't reveal whether email exists or not
            Session::setFlash('success', 'If an account with that email exists, a password reset link has been sent.');
        }

        header('Location: /login');
        exit;
    }

    /**
     * Redirect to login page
     */
    private function redirectToLogin()
    {
        header('Location: /login');
        exit;
    }

    /**
     * Redirect to registration page
     */
    private function redirectToRegister()
    {
        header('Location: /register');
        exit;
    }

    /**
     * Redirect to forgot password page
     */
    private function redirectToForgotPassword()
    {
        header('Location: /forgot-password');
        exit;
    }
}