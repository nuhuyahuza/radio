<?php

namespace App\Middleware;

use App\Utils\Session;

/**
 * Authentication Middleware
 * Handles user authentication and authorization
 */
class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function requireAuth()
    {
        if (!Session::isLoggedIn()) {
            self::redirectToLogin();
        }

        // Check session timeout
        if (!Session::checkTimeout()) {
            Session::setFlash('error', 'Your session has expired. Please log in again.');
            self::redirectToLogin();
        }
    }

    /**
     * Check if user has specific role
     */
    public static function requireRole($role)
    {
        self::requireAuth();

        if (!Session::hasRole($role)) {
            self::redirectToUnauthorized();
        }
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function requireAnyRole($roles)
    {
        self::requireAuth();

        if (!Session::hasAnyRole($roles)) {
            self::redirectToUnauthorized();
        }
    }

    /**
     * Check if user is admin
     */
    public static function requireAdmin()
    {
        self::requireRole('admin');
    }

    /**
     * Check if user is station manager or admin
     */
    public static function requireManager()
    {
        self::requireAnyRole(['admin', 'station_manager']);
    }

    /**
     * Check if user is advertiser or higher
     */
    public static function requireAdvertiser()
    {
        self::requireAnyRole(['admin', 'station_manager', 'advertiser']);
    }

    /**
     * Redirect to login page
     */
    private static function redirectToLogin()
    {
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
        Session::setFlash('redirect_after_login', $currentUrl);
        
        header('Location: /login');
        exit;
    }

    /**
     * Redirect to unauthorized page
     */
    private static function redirectToUnauthorized()
    {
        http_response_code(403);
        include __DIR__ . '/../../public/views/403.php';
        exit;
    }

    /**
     * Check if user can access admin area
     */
    public static function canAccessAdmin()
    {
        return Session::hasRole('admin');
    }

    /**
     * Check if user can access manager area
     */
    public static function canAccessManager()
    {
        return Session::hasAnyRole(['admin', 'station_manager']);
    }

    /**
     * Check if user can access advertiser area
     */
    public static function canAccessAdvertiser()
    {
        return Session::hasAnyRole(['admin', 'station_manager', 'advertiser']);
    }

    /**
     * Get user's dashboard URL based on role
     */
    public static function getDashboardUrl()
    {
        $user = Session::getUser();
        
        if (!$user) {
            return '/login';
        }
        
        switch ($user['role']) {
            case 'admin':
                return '/admin';
            case 'station_manager':
                return '/manager';
            case 'advertiser':
                return '/advertiser';
            default:
                return '/login';
        }
    }

    /**
     * Check if user can perform action on resource
     */
    public static function canPerformAction($action, $resource = null)
    {
        $user = Session::getUser();
        
        if (!$user) {
            return false;
        }

        $role = $user['role'];

        // Define permissions matrix
        $permissions = [
            'admin' => [
                'create_user' => true,
                'edit_user' => true,
                'delete_user' => true,
                'view_all_bookings' => true,
                'approve_booking' => true,
                'reject_booking' => true,
                'create_slot' => true,
                'edit_slot' => true,
                'delete_slot' => true,
                'view_reports' => true,
                'export_data' => true
            ],
            'station_manager' => [
                'create_user' => false,
                'edit_user' => false,
                'delete_user' => false,
                'view_all_bookings' => true,
                'approve_booking' => true,
                'reject_booking' => true,
                'create_slot' => true,
                'edit_slot' => true,
                'delete_slot' => true,
                'view_reports' => true,
                'export_data' => false
            ],
            'advertiser' => [
                'create_user' => false,
                'edit_user' => false,
                'delete_user' => false,
                'view_all_bookings' => false,
                'approve_booking' => false,
                'reject_booking' => false,
                'create_slot' => false,
                'edit_slot' => false,
                'delete_slot' => false,
                'view_reports' => false,
                'export_data' => false
            ]
        ];

        return $permissions[$role][$action] ?? false;
    }

    /**
     * Log user activity
     */
    public static function logActivity($action, $details = null)
    {
        $user = Session::getUser();
        
        if (!$user) {
            return;
        }

        // This would typically log to audit_logs table
        // For now, we'll just store in session for demo
        $activities = Session::get('user_activities', []);
        $activities[] = [
            'action' => $action,
            'details' => $details,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        // Keep only last 10 activities
        $activities = array_slice($activities, -10);
        Session::set('user_activities', $activities);
    }
}