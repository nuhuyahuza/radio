<?php

namespace App\Middleware;

use App\Utils\Session;

/**
 * Security Middleware
 * Handles various security measures including CSRF, XSS, and rate limiting
 */
class SecurityMiddleware
{
    private static $rateLimitStore = [];
    private static $maxRequests = 100; // Max requests per hour
    private static $timeWindow = 3600; // 1 hour in seconds

    /**
     * Apply security headers
     */
    public static function applySecurityHeaders()
    {
        // Prevent XSS attacks
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('Content-Type: text/html; charset=UTF-8');
        
        // Strict Transport Security (HTTPS only)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' https://cdnjs.cloudflare.com; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none';";
        header("Content-Security-Policy: $csp");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken($token)
    {
        if (!Session::hasFlash('csrf_token')) {
            return false;
        }
        
        $sessionToken = Session::getFlash('csrf_token');
        return hash_equals($sessionToken, $token);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken()
    {
        $token = bin2hex(random_bytes(32));
        Session::setFlash('csrf_token', $token);
        return $token;
    }

    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        if (is_string($data)) {
            // Remove null bytes
            $data = str_replace(chr(0), '', $data);
            
            // Trim whitespace
            $data = trim($data);
            
            // Convert special characters to HTML entities
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password)
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }

    /**
     * Rate limiting
     */
    public static function checkRateLimit($identifier = null)
    {
        if ($identifier === null) {
            $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $currentTime = time();
        $key = $identifier . '_' . floor($currentTime / self::$timeWindow);
        
        if (!isset(self::$rateLimitStore[$key])) {
            self::$rateLimitStore[$key] = 0;
        }
        
        self::$rateLimitStore[$key]++;
        
        if (self::$rateLimitStore[$key] > self::$maxRequests) {
            return false;
        }
        
        return true;
    }

    /**
     * Clean up old rate limit entries
     */
    public static function cleanupRateLimit()
    {
        $currentTime = time();
        $cutoff = $currentTime - (self::$timeWindow * 2);
        
        foreach (self::$rateLimitStore as $key => $count) {
            $timestamp = (int) substr($key, strrpos($key, '_') + 1);
            if ($timestamp < $cutoff) {
                unset(self::$rateLimitStore[$key]);
            }
        }
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) // 5MB default
    {
        $errors = [];
        
        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = 'Invalid file upload';
            return $errors;
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file uploaded';
                return $errors;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'File too large';
                return $errors;
            default:
                $errors[] = 'Unknown upload error';
                return $errors;
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = 'File too large';
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Invalid file type';
        }
        
        return $errors;
    }

    /**
     * Generate secure random string
     */
    public static function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Hash sensitive data
     */
    public static function hashSensitiveData($data)
    {
        return hash('sha256', $data . self::getApplicationSalt());
    }

    /**
     * Get application salt
     */
    private static function getApplicationSalt()
    {
        return $_ENV['APP_SALT'] ?? 'default_salt_change_in_production';
    }

    /**
     * Validate IP address
     */
    public static function validateIpAddress($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * Check if request is from allowed origin
     */
    public static function validateOrigin($allowedOrigins = [])
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (empty($allowedOrigins)) {
            $allowedOrigins = [$_ENV['APP_URL'] ?? 'http://localhost'];
        }
        
        return in_array($origin, $allowedOrigins);
    }

    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = [])
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        error_log('SECURITY: ' . json_encode($logEntry));
    }

    /**
     * Block suspicious activity
     */
    public static function blockSuspiciousActivity($reason)
    {
        self::logSecurityEvent('suspicious_activity_blocked', ['reason' => $reason]);
        
        http_response_code(403);
        include __DIR__ . '/../../public/views/403.php';
        exit;
    }

    /**
     * Validate session security
     */
    public static function validateSessionSecurity()
    {
        // Check if session is valid
        if (!Session::isValid()) {
            Session::destroy();
            return false;
        }
        
        // Check for session hijacking
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $sessionIp = Session::get('user_ip');
        
        if ($sessionIp && $sessionIp !== $currentIp) {
            self::logSecurityEvent('session_hijacking_attempt', [
                'session_ip' => $sessionIp,
                'current_ip' => $currentIp
            ]);
            Session::destroy();
            return false;
        }
        
        // Update session IP if not set
        if (!$sessionIp) {
            Session::set('user_ip', $currentIp);
        }
        
        return true;
    }

    /**
     * Escape output for HTML
     */
    public static function escapeHtml($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Escape output for JavaScript
     */
    public static function escapeJs($string)
    {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Escape output for SQL (use prepared statements instead)
     */
    public static function escapeSql($string)
    {
        // This is a basic escape function, but prepared statements should be used instead
        return addslashes($string);
    }
}

