<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = str_replace('/api', '', $uri);
$method = $_SERVER['REQUEST_METHOD'];

// ── Routes ────────────────────────────────────────────
if ($uri === '/auth/signup' && $method === 'POST') {
    require_once __DIR__ . '/auth/signup.php';

} elseif ($uri === '/auth/login' && $method === 'POST') {
    require_once __DIR__ . '/auth/login.php';

} else {
    http_response_code(404);
    echo json_encode(["message" => "Route not found"]);
}