<?php

namespace App\Utils;

use App\Middleware\SecurityMiddleware;

/**
 * Security Initializer
 * Initializes security measures for the application
 */
class SecurityInitializer
{
    private static $initialized = false;

    /**
     * Initialize security measures
     */
    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }

        // Load security configuration
        $config = self::loadSecurityConfig();

        // Apply security headers
        self::applySecurityHeaders($config);

        // Initialize session security
        self::initializeSessionSecurity($config);

        // Setup error handling
        self::setupErrorHandling($config);

        // Initialize logging
        self::initializeLogging($config);

        // Setup rate limiting
        self::setupRateLimiting($config);

        // Validate request
        self::validateRequest($config);

        self::$initialized = true;
    }

    /**
     * Load security configuration
     */
    private static function loadSecurityConfig()
    {
        $configFile = __DIR__ . '/../../config/security.php';
        
        if (file_exists($configFile)) {
            return include $configFile;
        }

        // Return default configuration
        return [
            'headers' => [
                'x_content_type_options' => 'nosniff',
                'x_frame_options' => 'DENY',
                'x_xss_protection' => '1; mode=block',
            ],
            'session' => [
                'timeout' => 3600,
                'httponly' => true,
                'secure' => true,
                'samesite' => 'Strict',
            ],
            'rate_limiting' => [
                'enabled' => true,
                'max_requests' => 100,
                'time_window' => 3600,
            ],
            'logging' => [
                'enabled' => true,
                'log_level' => 'info',
            ],
        ];
    }

    /**
     * Apply security headers
     */
    private static function applySecurityHeaders($config)
    {
        $headers = $config['headers'] ?? [];

        foreach ($headers as $header => $value) {
            if ($value) {
                $headerName = 'X-' . str_replace('_', '-', ucwords($header, '_'));
                header("$headerName: $value");
            }
        }

        // Additional security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    /**
     * Initialize session security
     */
    private static function initializeSessionSecurity($config)
    {
        $sessionConfig = $config['session'] ?? [];

        if (session_status() === PHP_SESSION_NONE) {
            // Configure session security
            ini_set('session.cookie_httponly', $sessionConfig['httponly'] ? 1 : 0);
            ini_set('session.cookie_secure', $sessionConfig['secure'] ? 1 : 0);
            ini_set('session.use_strict_mode', $sessionConfig['use_strict_mode'] ? 1 : 0);
            ini_set('session.cookie_samesite', $sessionConfig['samesite'] ?? 'Strict');
            
            // Set session timeout
            if (isset($sessionConfig['timeout'])) {
                ini_set('session.gc_maxlifetime', $sessionConfig['timeout']);
            }

            session_start();
        }
    }

    /**
     * Setup error handling
     */
    private static function setupErrorHandling($config)
    {
        $errorConfig = $config['error_handling'] ?? [];

        if (isset($errorConfig['display_errors'])) {
            ini_set('display_errors', $errorConfig['display_errors'] ? 1 : 0);
        }

        if (isset($errorConfig['log_errors'])) {
            ini_set('log_errors', $errorConfig['log_errors'] ? 1 : 0);
        }

        if (isset($errorConfig['error_reporting'])) {
            error_reporting($errorConfig['error_reporting']);
        }
    }

    /**
     * Initialize logging
     */
    private static function initializeLogging($config)
    {
        $loggingConfig = $config['logging'] ?? [];

        if ($loggingConfig['enabled'] ?? true) {
            $logFile = $loggingConfig['log_file'] ?? 'logs/security.log';
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
        }
    }

    /**
     * Setup rate limiting
     */
    private static function setupRateLimiting($config)
    {
        $rateLimitConfig = $config['rate_limiting'] ?? [];

        if ($rateLimitConfig['enabled'] ?? true) {
            $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $excludedIps = $rateLimitConfig['excluded_ips'] ?? [];

            if (!in_array($identifier, $excludedIps)) {
                if (!SecurityMiddleware::checkRateLimit($identifier)) {
                    http_response_code(429);
                    include __DIR__ . '/../../public/views/429.php';
                    exit;
                }
            }
        }
    }

    /**
     * Validate request
     */
    private static function validateRequest($config)
    {
        // Check for suspicious patterns
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $queryString = $_SERVER['QUERY_STRING'] ?? '';

        // Block common attack patterns
        $suspiciousPatterns = [
            '/\.\.\//',           // Directory traversal
            '/<script/i',         // XSS attempts
            '/union.*select/i',   // SQL injection
            '/drop.*table/i',     // SQL injection
            '/insert.*into/i',    // SQL injection
            '/delete.*from/i',    // SQL injection
            '/update.*set/i',     // SQL injection
            '/exec\(/i',          // Command injection
            '/system\(/i',        // Command injection
            '/eval\(/i',          // Code injection
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $requestUri . $queryString)) {
                SecurityMiddleware::logSecurityEvent('suspicious_request_blocked', [
                    'pattern' => $pattern,
                    'uri' => $requestUri,
                    'query' => $queryString
                ]);
                
                SecurityMiddleware::blockSuspiciousActivity('Suspicious request pattern detected');
            }
        }

        // Validate request method
        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

        if (!in_array($requestMethod, $allowedMethods)) {
            SecurityMiddleware::logSecurityEvent('invalid_request_method', [
                'method' => $requestMethod
            ]);
            
            http_response_code(405);
            exit;
        }

        // Check request size
        $maxRequestSize = 8 * 1024 * 1024; // 8MB
        $contentLength = $_SERVER['CONTENT_LENGTH'] ?? 0;

        if ($contentLength > $maxRequestSize) {
            SecurityMiddleware::logSecurityEvent('request_too_large', [
                'size' => $contentLength,
                'max_size' => $maxRequestSize
            ]);
            
            http_response_code(413);
            exit;
        }
    }

    /**
     * Check maintenance mode
     */
    public static function checkMaintenanceMode($config)
    {
        $maintenanceConfig = $config['maintenance'] ?? [];

        if ($maintenanceConfig['enabled'] ?? false) {
            $allowedIps = $maintenanceConfig['allowed_ips'] ?? [];
            $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

            if (!in_array($currentIp, $allowedIps)) {
                http_response_code(503);
                $message = $maintenanceConfig['message'] ?? 'System is under maintenance. Please try again later.';
                include __DIR__ . '/../../public/views/503.php';
                exit;
            }
        }
    }

    /**
     * Validate CSRF token for POST requests
     */
    public static function validateCsrfForPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!SecurityMiddleware::validateCsrfToken($token)) {
                SecurityMiddleware::logSecurityEvent('csrf_token_invalid', [
                    'token' => $token,
                    'uri' => $_SERVER['REQUEST_URI'] ?? ''
                ]);
                
                http_response_code(403);
                include __DIR__ . '/../../public/views/403.php';
                exit;
            }
        }
    }
}

