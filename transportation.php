<?php
header('Content-Type: application/json');

// DB connection config
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

// Warehouses list for origin dropdown
if (isset($_GET['warehouses']) && $_GET['warehouses'] == 1) {
    $warehouses = [];
    $result = $conn->query("SELECT name FROM warehouses ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $warehouses[] = $row['name'];
    }
    echo json_encode(['success' => true, 'warehouses' => $warehouses]);
    $conn->close();
    exit;
}

// Products list for product dropdown
if (isset($_GET['products']) && $_GET['products'] == 1) {
    $products = [];
    $result = $conn->query("SELECT productName FROM products ORDER BY productName");
    while ($row = $result->fetch_assoc()) {
        $products[] = $row['productName'];
    }
    echo json_encode(['success' => true, 'products' => $products]);
    $conn->close();
    exit;
}

// Helper: fetch all transports + stats
function getTransportAndStats($conn) {
    $transports = [];
    $result = $conn->query("SELECT * FROM transportation ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()) {
        $transports[] = [
            'id' => $row['id'],
            'origin' => $row['origin'],
            'destination' => $row['destination'],
            'product' => $row['product'],
            'weight' => $row['weight'],
            'distance' => (int)$row['distance'],
            'temperature' => $row['temperature'],
            'humidity' => $row['humidity'],
            'vehicle' => $row['vehicle'],
            'driver' => $row['driver'],
            'schedule_date' => $row['schedule_date'],
            'schedule_time' => $row['schedule_time'],
            'priority' => $row['priority'],
            'status' => $row['status'],
            'costing' => (float)$row['costing'],
        ];
    }

    // Stats
    $totalRoutes = (int)$conn->query("SELECT COUNT(*) FROM transportation")->fetch_row()[0];
    $today = date('Y-m-d');
    $scheduledToday = (int)$conn->query("SELECT COUNT(*) FROM transportation WHERE schedule_date = '$today'")->fetch_row()[0];
    $availableVehicles = 4 - (int)$conn->query("SELECT COUNT(DISTINCT vehicle) FROM transportation WHERE status = 'Scheduled'")->fetch_row()[0];

    return [
        'success' => true,
        'transports' => $transports,
        'stats' => [
            'totalRoutes' => $totalRoutes,
            'scheduledToday' => $scheduledToday,
            'availableVehicles' => max($availableVehicles, 0),
        ]
    ];
}

// Handle DELETE request to delete transport by id (via POST method with action=delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (empty($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing transport id for deletion']);
        exit;
    }

    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM transportation WHERE id = ?");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete transportation record']);
        exit;
    }

    $stmt->close();

    echo json_encode(getTransportAndStats($conn));
    $conn->close();
    exit;
}

// Handle UPDATE transport (edit) via POST with action=update and id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $required = ['id', 'origin','destination','product','weight','distance','temperature','humidity','vehicle','driver','date','time','priority'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    $id = (int)$_POST['id'];
    $origin = $conn->real_escape_string($_POST['origin']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $product = $conn->real_escape_string($_POST['product']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $distance = (int)$_POST['distance'];
    $temperature = $conn->real_escape_string($_POST['temperature']);
    $humidity = $conn->real_escape_string($_POST['humidity']);
    $vehicle = $conn->real_escape_string($_POST['vehicle']);
    $driver = $conn->real_escape_string($_POST['driver']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $priority = $conn->real_escape_string($_POST['priority']);

    $status = 'Scheduled';
    $costing = $distance * 15; // cost formula

    $stmt = $conn->prepare("UPDATE transportation SET origin=?, destination=?, product=?, weight=?, distance=?, temperature=?, humidity=?, vehicle=?, driver=?, schedule_date=?, schedule_time=?, priority=?, status=?, costing=? WHERE id=?");
    $stmt->bind_param("ssssisssssssd i", $origin, $destination, $product, $weight, $distance, $temperature, $humidity, $vehicle, $driver, $date, $time, $priority, $status, $costing, $id);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update transportation record']);
        exit;
    }

    $stmt->close();

    echo json_encode(getTransportAndStats($conn));
    $conn->close();
    exit;
}

// Handle ADD transport (default POST without action)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['origin','destination','product','weight','distance','temperature','humidity','vehicle','driver','date','time','priority'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    $origin = $conn->real_escape_string($_POST['origin']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $product = $conn->real_escape_string($_POST['product']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $distance = (int)$_POST['distance'];
    $temperature = $conn->real_escape_string($_POST['temperature']);
    $humidity = $conn->real_escape_string($_POST['humidity']);
    $vehicle = $conn->real_escape_string($_POST['vehicle']);
    $driver = $conn->real_escape_string($_POST['driver']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $priority = $conn->real_escape_string($_POST['priority']);

    $status = 'Scheduled';
    $costing = $distance * 15; // your cost formula

    $stmt = $conn->prepare("INSERT INTO transportation (origin, destination, product, weight, distance, temperature, humidity, vehicle, driver, schedule_date, schedule_time, priority, status, costing) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssissssssssd", $origin, $destination, $product, $weight, $distance, $temperature, $humidity, $vehicle, $driver, $date, $time, $priority, $status, $costing);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add transportation record']);
        exit;
    }

    $stmt->close();

    echo json_encode(getTransportAndStats($conn));
    $conn->close();
    exit;
}

// For GET requests - return transports + stats
echo json_encode(getTransportAndStats($conn));
$conn->close();
exit;
