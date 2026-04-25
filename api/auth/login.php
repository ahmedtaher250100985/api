<?php
$conn = mysqli_connect("localhost", "root", "", "signup");

if (!$conn) {
    die(json_encode(["message" => "Connection failed"]));
}

// ── Read Body ──────────────────────────────────────────
$data = json_decode(file_get_contents("php://input"), true);

// ── Validate ───────────────────────────────────────────
if (!$data || !isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["message" => "All fields are required"]);
    exit;
}

$email    = trim($data['email']);
$password = $data['password'];

// ── Find User ──────────────────────────────────────────
$result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$user   = mysqli_fetch_assoc($result);

// ── Check User + Password ──────────────────────────────
if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid credentials"]);
    exit;
}

// ── Generate Token ─────────────────────────────────────
$token = bin2hex(random_bytes(32));

// ── Response ───────────────────────────────────────────
echo json_encode([
    "message" => "Login successful",
    "token"   => $token,
    "user"    => [
        "id"    => $user['id'],
        "name"  => $user['name'],
        "email" => $user['email'],
    ]
]);