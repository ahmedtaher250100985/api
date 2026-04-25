<?php
$conn = mysqli_connect("localhost", "root", "", "signup");

if (!$conn) {
    die(json_encode(["message" => "Connection failed"]));
}

// ── Read Body ──────────────────────────────────────────
$data = json_decode(file_get_contents("php://input"), true);

// ── Validate Fields ────────────────────────────────────
if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["message" => "All fields are required"]);
    exit;
}

$name     = trim($data['name']);
$email    = trim($data['email']);
$password = $data['password'];

// ── Email Validation ───────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid email format"]);
    exit;
}

// ── Check Email Exists ─────────────────────────────────
$check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

if (mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(["message" => "Email already exists"]);
    exit;
}

// ── Hash Password ──────────────────────────────────────
$hashed = password_hash($password, PASSWORD_DEFAULT);

// ── Insert ─────────────────────────────────────────────
$sql = "INSERT INTO users (name, email, password, created_at, updated_at) 
        VALUES ('$name', '$email', '$hashed', NOW(), NOW())";

if (mysqli_query($conn, $sql)) {
    $id = mysqli_insert_id($conn);

    echo json_encode([
        "message" => "User created successfully",
        "user"    => [
            "id"    => $id,
            "name"  => $name,
            "email" => $email,
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Something went wrong"]);
}