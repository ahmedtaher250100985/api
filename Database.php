<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$conn = mysqli_connect("localhost", "root", "", "signup");

if (!$conn) {
    die(json_encode(["message" => "Connection failed"]));
}

$data = json_decode(file_get_contents("php://input"), true);

// ── 1. Check all fields exist ──────────────────────────
if (!$data || !isset(
    $data['name'],
    $data['email'],
    $data['password'],
    $data['age'],
    $data['height'],
    $data['weight']
)) {
    http_response_code(400);
    echo json_encode(["message" => "All fields are required"]);
    exit;
}

$name     = trim($data['name']);
$email    = trim($data['email']);
$password = $data['password'];
$age      = $data['age'];
$height   = $data['height'];
$weight   = $data['weight'];

// ── 2. Password Validation ─────────────────────────────
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["message" => "Password must be at least 8 characters"]);
    exit;
}

if (!preg_match('/[A-Z]/', $password)) {
    http_response_code(400);
    echo json_encode(["message" => "Password must contain at least one uppercase letter"]);
    exit;
}

if (!preg_match('/[a-z]/', $password)) {
    http_response_code(400);
    echo json_encode(["message" => "Password must contain at least one lowercase letter"]);
    exit;
}

if (!preg_match('/[0-9]/', $password)) {
    http_response_code(400);
    echo json_encode(["message" => "Password must contain at least one number"]);
    exit;
}

// ── 3. Email Validation ────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid email format"]);
    exit;
}

// ── 4. Numbers Validation ──────────────────────────────
if (!is_numeric($age) || $age <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Age must be a valid number"]);
    exit;
}

if (!is_numeric($height) || $height <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Height must be a valid number"]);
    exit;
}

if (!is_numeric($weight) || $weight <= 0) {
    http_response_code(400);
    echo json_encode(["message" => "Weight must be a valid number"]);
    exit;
}

// ── 5. Check Email Exists ──────────────────────────────
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

if (mysqli_num_rows($check) > 0) {
    http_response_code(400);
    echo json_encode(["message" => "Email already exists"]);
    exit;
}

// ── 6. Hash Password & Insert ──────────────────────────
$hashed = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password, age, height, weight) 
        VALUES ('$name', '$email', '$hashed', '$age', '$height', '$weight')";

if (mysqli_query($conn, $sql)) {
    $id = mysqli_insert_id($conn);
    echo json_encode([
        "message" => "User created successfully",
        "user"    => [
            "id"     => $id,
            "name"   => $name,
            "email"  => $email,
            "age"    => $age,
            "height" => $height,
            "weight" => $weight
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Something went wrong"]);
}
?>