<?php
require_once __DIR__ . '/../../vendor/autoload.php';
\App\Utils\Session::start();
$existing = \App\Utils\Session::getCsrfToken();
$token = $existing ?: \App\Utils\Session::setCsrfToken();
header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['csrf_token' => $token]);
