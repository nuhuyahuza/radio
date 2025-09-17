<?php

/**
 * Security Configuration
 * Centralized security settings for the application
 */

return [
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'token_length' => 32,
        'regenerate_on_login' => true,
        'regenerate_on_role_change' => true,
    ],

    // Rate Limiting
    'rate_limiting' => [
        'enabled' => true,
        'max_requests' => 100,
        'time_window' => 3600, // 1 hour in seconds
        'excluded_ips' => [
            '127.0.0.1',
            '::1'
        ],
    ],

    // Password Security
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],

    // Session Security
    'session' => [
        'timeout' => 3600, // 1 hour in seconds
        'regenerate_interval' => 300, // 5 minutes
        'httponly' => true,
        'secure' => true,
        'samesite' => 'Strict',
        'use_strict_mode' => true,
    ],

    // File Upload Security
    'file_upload' => [
        'max_size' => 5242880, // 5MB
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'text/plain',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'scan_uploads' => true,
        'quarantine_suspicious' => true,
    ],

    // Input Validation
    'input_validation' => [
        'sanitize_all_input' => true,
        'max_string_length' => 1000,
        'max_array_depth' => 10,
        'block_sql_injection' => true,
        'block_xss' => true,
    ],

    // Security Headers
    'headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'strict_transport_security' => 'max-age=31536000; includeSubDomains',
        'content_security_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com; connect-src 'self'; frame-ancestors 'none';",
        'referrer_policy' => 'strict-origin-when-cross-origin',
    ],

    // Logging
    'logging' => [
        'enabled' => true,
        'log_level' => 'info',
        'log_file' => 'logs/security.log',
        'log_rotation' => true,
        'max_log_size' => 10485760, // 10MB
        'max_log_files' => 5,
    ],

    // IP Whitelist/Blacklist
    'ip_filtering' => [
        'enabled' => false,
        'whitelist' => [],
        'blacklist' => [],
        'block_suspicious_ips' => true,
    ],

    // Two-Factor Authentication
    '2fa' => [
        'enabled' => false,
        'required_for_admin' => true,
        'required_for_manager' => false,
        'backup_codes_count' => 10,
        'issuer' => 'Zaa Radio',
    ],

    // Encryption
    'encryption' => [
        'algorithm' => 'AES-256-GCM',
        'key_length' => 32,
        'iv_length' => 16,
        'tag_length' => 16,
    ],

    // Database Security
    'database' => [
        'use_prepared_statements' => true,
        'escape_queries' => true,
        'limit_query_complexity' => true,
        'max_query_time' => 30, // seconds
    ],

    // API Security
    'api' => [
        'rate_limit' => 1000, // requests per hour
        'require_https' => true,
        'cors_enabled' => false,
        'allowed_origins' => [],
        'api_key_required' => false,
    ],

    // Error Handling
    'error_handling' => [
        'display_errors' => false,
        'log_errors' => true,
        'error_reporting' => E_ALL,
        'custom_error_pages' => true,
    ],

    // Maintenance Mode
    'maintenance' => [
        'enabled' => false,
        'allowed_ips' => [
            '127.0.0.1',
            '::1'
        ],
        'message' => 'System is under maintenance. Please try again later.',
    ],
];

