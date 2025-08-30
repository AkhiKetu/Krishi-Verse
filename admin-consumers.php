<?php
header('Content-Type: application/json');

// Database config - adjust if needed
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

// Connect
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new consumer

    // Get POST data safely
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Validate required
    if (!$name || !$type || !in_array($type, ['farmer', 'supplier', 'customer']) || !in_array($status, ['active', 'inactive'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO consumers (name, type, email, phone, location, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $type, $email, $phone, $location, $status);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add consumer']);
        exit;
    }

    $stmt->close();
    // After insertion, return updated list and counts
    echo json_encode(getConsumersAndCounts($conn));
    $conn->close();
    exit;
}



/*  add a new consumer (original)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trying to add a new consumer

    // Get data from the form
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Simple validation
    if (!$name || !$type || !in_array($type, ['farmer', 'supplier', 'customer']) || !in_array($status, ['active', 'inactive'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    // Prepare SQL to insert new consumer
    $stmt = $conn->prepare("INSERT INTO consumers (name, type, email, phone, location, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $type, $email, $phone, $location, $status);

    // Execute SQL
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add consumer']);
        exit;
    }

    $stmt->close();

    // Send back updated list
    echo json_encode(getConsumersAndCounts($conn));

    // Close connection
    $conn->close();
    exit;
}

*/



// For GET requests, return all consumers + counts
echo json_encode(getConsumersAndCounts($conn));
$conn->close();
exit;

// Helper function
function getConsumersAndCounts($conn) {
    $result = $conn->query("SELECT * FROM consumers ORDER BY created_at DESC");
    $consumers = [];
    while ($row = $result->fetch_assoc()) {
        $consumers[] = [
            'name' => $row['name'],
            'type' => $row['type'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'location' => $row['location'],
            'status' => $row['status']
        ];
    }
    // Counts
    $counts = [
        'farmers' => (int)$conn->query("SELECT COUNT(*) FROM consumers WHERE type='farmer'")->fetch_row()[0],
        'suppliers' => (int)$conn->query("SELECT COUNT(*) FROM consumers WHERE type='supplier'")->fetch_row()[0],
        'customers' => (int)$conn->query("SELECT COUNT(*) FROM consumers WHERE type='customer'")->fetch_row()[0],
        'newThisMonth' => (int)$conn->query("SELECT COUNT(*) FROM consumers WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetch_row()[0],
    ];
    return ['success' => true, 'consumers' => $consumers, 'counts' => $counts];
}
