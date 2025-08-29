<?php
header('Content-Type: application/json');
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "krishi-verse";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $manager = $_POST['manager'] ?? '';
    $type = $_POST['type'] ?? '';

    if ($name && $location && $capacity && $manager && $type) {
        $stmt = $conn->prepare("INSERT INTO warehouses (name, location, capacity, manager, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $name, $location, $capacity, $manager, $type);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to insert data"]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields"]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM warehouses ORDER BY id DESC");
    $warehouses = [];
    while ($row = $result->fetch_assoc()) {
        $warehouses[] = $row;
    }
    echo json_encode($warehouses);
    exit();
}
?>
