<?php
/**
 * Test Timeout Configuration
 * Verify PHP timeout settings are adequate
 */

header('Content-Type: application/json');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_settings' => [
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'memory_limit' => ini_get('memory_limit'),
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ],
    'recommendations' => []
];

// Check if settings are adequate
$maxExecTime = (int)ini_get('max_execution_time');
if ($maxExecTime > 0 && $maxExecTime < 60) {
    $results['recommendations'][] = [
        'setting' => 'max_execution_time',
        'current' => $maxExecTime . ' seconds',
        'recommended' => '120 seconds',
        'issue' => 'May timeout with large bookings',
        'fix' => 'Edit php.ini or .htaccess to increase to 120'
    ];
    $results['status'] = 'WARNING';
} else {
    $results['status'] = 'OK';
}

// Test simulated booking processing time
$startTime = microtime(true);

// Simulate processing 100 sessions
for ($i = 0; $i < 100; $i++) {
    // Simulate database operation
    usleep(10000); // 10ms per session
}

$endTime = microtime(true);
$processingTime = round(($endTime - $startTime) * 1000, 2);

$results['performance_test'] = [
    'simulated_sessions' => 100,
    'processing_time_ms' => $processingTime,
    'estimated_time_for_300_sessions' => round($processingTime * 3, 2) . ' ms',
    'will_timeout' => $processingTime * 3 > ($maxExecTime * 1000) ? 'YES' : 'NO'
];

// Check if .htaccess exists
$htaccessPath = __DIR__ . '/.htaccess';
$results['htaccess'] = [
    'exists' => file_exists($htaccessPath),
    'path' => $htaccessPath,
    'readable' => file_exists($htaccessPath) ? is_readable($htaccessPath) : false
];

if (!file_exists($htaccessPath)) {
    $results['recommendations'][] = [
        'setting' => '.htaccess',
        'issue' => 'Missing .htaccess file',
        'fix' => 'Create .htaccess with timeout settings (see TIMEOUT_FIX.md)'
    ];
    $results['status'] = 'WARNING';
}

echo json_encode($results, JSON_PRETTY_PRINT);


