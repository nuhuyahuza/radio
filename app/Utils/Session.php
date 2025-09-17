<?php

namespace App\Utils;

/**
 * Session Management Utility
 * Handles session operations and security
 */
class Session
{
    /**
     * Start session if not already started
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure session security
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.use_strict_mode', 1);
            
            session_start();
        }
    }

    /**
     * Set session data
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session data
     */
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session data
     */
    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Clear all session data
     */
    public static function clear()
    {
        self::start();
        $_SESSION = [];
    }

    /**
     * Destroy session
     */
    public static function destroy()
    {
        self::start();
        session_destroy();
    }

    /**
     * Regenerate session ID
     */
    public static function regenerate()
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Set user session data
     */
    public static function setUser($user)
    {
        self::set('user_id', $user['id']);
        self::set('user_name', $user['name']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role']);
        self::set('user_company', $user['company'] ?? '');
        self::set('logged_in', true);
        self::set('login_time', time());
    }

    /**
     * Get current user data
     */
    public static function getUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => self::get('user_id'),
            'name' => self::get('user_name'),
            'email' => self::get('user_email'),
            'role' => self::get('user_role'),
            'company' => self::get('user_company'),
            'login_time' => self::get('login_time')
        ];
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        return self::get('logged_in', false) === true;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role)
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        return self::get('user_role') === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($roles)
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $userRole = self::get('user_role');
        return in_array($userRole, (array)$roles);
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        self::clear();
        self::destroy();
    }

    /**
     * Set flash message
     */
    public static function setFlash($key, $message)
    {
        self::set("flash_$key", $message);
    }

    /**
     * Get and clear flash message
     */
    public static function getFlash($key)
    {
        $message = self::get("flash_$key");
        self::remove("flash_$key");
        return $message;
    }

    /**
     * Check if flash message exists
     */
    public static function hasFlash($key)
    {
        return self::has("flash_$key");
    }

    /**
     * Set CSRF token
     */
    public static function setCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        self::set('csrf_token', $token);
        return $token;
    }

    /**
     * Get CSRF token
     */
    public static function getCsrfToken()
    {
        return self::get('csrf_token');
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token)
    {
        return hash_equals(self::getCsrfToken(), $token);
    }

    /**
     * Check session timeout
     */
    public static function checkTimeout($timeoutMinutes = 60)
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        $loginTime = self::get('login_time');
        $timeout = $timeoutMinutes * 60; // Convert to seconds

        if (time() - $loginTime > $timeout) {
            self::logout();
            return false;
        }

        return true;
    }

    /**
     * Check if session is valid
     */
    public static function isValid()
    {
        return session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION);
    }

    /**
     * Set secure session parameters
     */
    public static function setSecureParams()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
        }
    }

    /**
     * Validate session security
     */
    public static function validateSecurity()
    {
        // Check if session is valid
        if (!self::isValid()) {
            self::destroy();
            return false;
        }
        
        // Check for session hijacking
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $sessionIp = self::get('user_ip');
        
        if ($sessionIp && $sessionIp !== $currentIp) {
            self::destroy();
            return false;
        }
        
        // Update session IP if not set
        if (!$sessionIp) {
            self::set('user_ip', $currentIp);
        }
        
        return true;
    }
}
