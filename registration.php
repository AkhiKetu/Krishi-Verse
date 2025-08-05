<?php
header('Content-Type: application/json');

// Step 1: DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

// Step 2: Get form data
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$designation = $_POST['designation'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Step 3: Password match check
if ($password !== $confirm_password) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit();
}

// Step 4: Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Step 5: Insert into database
$sql = "INSERT INTO users (full_name, email, username, designation, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssss", $full_name, $email, $username, $designation, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful! Redirecting...']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
