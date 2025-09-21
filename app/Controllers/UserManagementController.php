<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\Session;
use App\Database\Database;

/**
 * User Management Controller
 * Handles admin operations for managing users
 */
class UserManagementController
{
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    /**
     * Show user management dashboard
     */
    public function showUserManagement()
    {
        $users = $this->userModel->getAllUsers();
        include __DIR__ . '/../../public/views/admin/users.php';
    }

    /**
     * Show user details
     */
    public function showUserDetails($userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            header('Location: /admin/users');
            exit;
        }

        include __DIR__ . '/../../public/views/admin/user-details.php';
    }

    /**
     * Show create user form
     */
    public function showCreateUser()
    {
        // Set variables for the view
        $isEdit = false;
        $currentPage = 'users';
        $formAction = '/admin/users/create';
        
        include __DIR__ . '/../../public/views/admin/user-form.php';
    }

    /**
     * Show edit user form
     */
    public function showEditUser($userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            header('Location: /admin/users');
            exit;
        }

        // Set variables for the view
        $isEdit = true;
        $currentPage = 'users';
        $formAction = "/admin/users/edit/{$userId}";
        
        include __DIR__ . '/../../public/views/admin/user-form.php';
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        error_log("UserManagementController::createUser called");
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("POST request received for user creation");
            // CSRF protection
            $csrfToken = $_POST['csrf_token'] ?? '';
            error_log("CSRF token from form: " . $csrfToken);
            error_log("Session CSRF token: " . Session::getCsrfToken());
            if (!Session::verifyCsrfToken($csrfToken)) {
                error_log("CSRF token validation failed");
                Session::setFlash('error', 'Invalid CSRF token.');
                $this->redirectToUsers();
            }
            error_log("CSRF token validation passed");

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (!$name || !$email || !$password || !$role) {
                Session::setFlash('error', 'Please fill in all required fields.');
                Session::setFlash('old', $_POST);
                $this->redirectToCreateUser();
            }

            // Validate role
            if (!in_array($role, ['admin', 'station_manager', 'advertiser'])) {
                Session::setFlash('error', 'Invalid role selected.');
                Session::setFlash('old', $_POST);
                $this->redirectToCreateUser();
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                Session::setFlash('error', 'Email already exists.');
                Session::setFlash('old', $_POST);
                $this->redirectToCreateUser();
            }

            try {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'phone' => $phone,
                    'company' => $company,
                    'is_active' => $isActive,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ];

                error_log("Creating user with data: " . json_encode($userData));
                $userId = $this->userModel->createUser($userData);
                error_log("User created with ID: " . $userId);

                // Log activity
                \App\Middleware\AuthMiddleware::logActivity('user_created', "New user created: $email with role $role");

                Session::setFlash('success', 'User created successfully.');
                header("Location: /admin/users/$userId");
                exit;

            } catch (\Exception $e) {
                Session::setFlash('error', 'Failed to create user: ' . $e->getMessage());
                error_log("User Creation Error: " . $e->getMessage());
                $this->redirectToCreateUser();
            }
        } else {
            $this->redirectToCreateUser();
        }
    }

    /**
     * Update user
     */
    public function updateUser($userId)
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF protection
            if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Invalid CSRF token.');
                $this->redirectToUsers();
            }

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
            $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (!$name || !$email || !$role) {
                Session::setFlash('error', 'Please fill in all required fields.');
                header("Location: /admin/users/edit/$userId");
                exit;
            }

            // Validate role
            if (!in_array($role, ['admin', 'station_manager', 'advertiser'])) {
                Session::setFlash('error', 'Invalid role selected.');
                header("Location: /admin/users/edit/$userId");
                exit;
            }

            // Check if email already exists for another user
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser['id'] != $userId) {
                Session::setFlash('error', 'Email already exists for another user.');
                header("Location: /admin/users/edit/$userId");
                exit;
            }

            try {
                $userData = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'phone' => $phone,
                    'company' => $company,
                    'is_active' => $isActive
                ];

                // Only update password if provided
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                if (!empty($password)) {
                    $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
                }

                $this->userModel->updateUser($userId, $userData);

                // Log activity
                \App\Middleware\AuthMiddleware::logActivity('user_updated', "User updated: $email");

                Session::setFlash('success', 'User updated successfully.');
                header("Location: /admin/users/edit/$userId");
                exit;

            } catch (\Exception $e) {
                Session::setFlash('error', 'Failed to update user: ' . $e->getMessage());
                error_log("User Update Error: " . $e->getMessage());
                header("Location: /admin/users/edit/$userId");
                exit;
            }
        } else {
            $this->redirectToEditUser($userId);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF protection
            if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Invalid CSRF token.');
                $this->redirectToUsers();
            }

            try {
                $user = $this->userModel->find($userId);
                if (!$user) {
                    Session::setFlash('error', 'User not found.');
                    $this->redirectToUsers();
                }

                // Prevent deleting the last admin
                if ($user['role'] === 'admin') {
                    $adminCount = $this->userModel->countUsersByRole('admin');
                    if ($adminCount <= 1) {
                        Session::setFlash('error', 'Cannot delete the last admin user.');
                        $this->redirectToUsers();
                    }
                }

                $this->userModel->deleteUser($userId);

                // Log activity
                \App\Middleware\AuthMiddleware::logActivity('user_deleted', "User deleted: {$user['email']}");

                Session::setFlash('success', 'User deleted successfully.');
                header('Location: /admin/users');
                exit;

            } catch (\Exception $e) {
                Session::setFlash('error', 'Failed to delete user: ' . $e->getMessage());
                error_log("User Deletion Error: " . $e->getMessage());
                $this->redirectToUsers();
            }
        } else {
            $this->redirectToUsers();
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus($userId)
    {
        Session::start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF protection
            if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                Session::setFlash('error', 'Invalid CSRF token.');
                $this->redirectToUsers();
            }

            try {
                $user = $this->userModel->find($userId);
                if (!$user) {
                    Session::setFlash('error', 'User not found.');
                    $this->redirectToUsers();
                }

                // Prevent deactivating the last admin
                if ($user['role'] === 'admin' && $user['is_active']) {
                    $activeAdminCount = $this->userModel->countActiveUsersByRole('admin');
                    if ($activeAdminCount <= 1) {
                        Session::setFlash('error', 'Cannot deactivate the last active admin user.');
                        $this->redirectToUsers();
                    }
                }

                $newStatus = $user['is_active'] ? 0 : 1;
                $this->userModel->updateUser($userId, ['is_active' => $newStatus]);

                $statusText = $newStatus ? 'activated' : 'deactivated';
                
                // Log activity
                \App\Middleware\AuthMiddleware::logActivity('user_status_changed', "User $statusText: {$user['email']}");

                Session::setFlash('success', "User $statusText successfully.");
                header('Location: /admin/users');
                exit;

            } catch (\Exception $e) {
                Session::setFlash('error', 'Failed to update user status: ' . $e->getMessage());
                error_log("User Status Toggle Error: " . $e->getMessage());
                $this->redirectToUsers();
            }
        } else {
            $this->redirectToUsers();
        }
    }

    /**
     * Get users data for AJAX requests
     */
    public function getUsersData()
    {
        header('Content-Type: application/json');

        try {
            $users = $this->userModel->getAllUsers();
            
            // Transform data for DataTables
            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'id' => $user['id'],
                    'name' => htmlspecialchars($user['name']),
                    'email' => htmlspecialchars($user['email']),
                    'role' => ucfirst($user['role']),
                    'phone' => htmlspecialchars($user['phone'] ?? ''),
                    'company' => htmlspecialchars($user['company'] ?? ''),
                    'is_active' => $user['is_active'] ? 'Active' : 'Inactive',
                    'created_at' => date('M j, Y', strtotime($user['created_at'])),
                    'last_login' => $user['last_login_at'] ? date('M j, Y g:i A', strtotime($user['last_login_at'])) : 'Never'
                ];
            }

            echo json_encode([
                'data' => $data
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to fetch users data',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect to users list
     */
    private function redirectToUsers()
    {
        header('Location: /admin/users');
        exit;
    }

    /**
     * Redirect to create user form
     */
    private function redirectToCreateUser()
    {
        header('Location: /admin/users/create');
        exit;
    }

    /**
     * Redirect to edit user form
     */
    private function redirectToEditUser($userId)
    {
        header("Location: /admin/users/edit/$userId");
        exit;
    }
}