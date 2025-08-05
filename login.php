<?php
header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$usernameOrEmail = $data['username'] ?? '';
$passwordInput = $data['password'] ?? '';
$designation = $data['designation'] ?? '';

// Check empty fields
if (!$usernameOrEmail || !$passwordInput || !$designation) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Query DB
$sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND designation = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $usernameOrEmail, $usernameOrEmail, $designation);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($passwordInput, $user['password'])) {
        // Redirect based on designation
        $redirectMap = [
            'warehouseManager' => 'dashboard.html',
            'supplier' => 'supplierDashboard.html',
            'transporter' => 'transportDashboard.html',
            'distributor' => 'distributorDashboard.html',
            'retailer' => 'retailerDashboard.html'
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'username' => $user['username'],
            'redirect' => $redirectMap[$designation] ?? 'dashboard.html'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid credentials or designation. Please try again.']);
}

$stmt->close();
$conn->close();
?>
