<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch ($action) {
        case 'getBatchIds':
            // Get all active products for batch ID selection
            $sql = "SELECT productID, productName FROM products WHERE status = 'in-stock'";
            $result = $conn->query($sql);
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
            break;

        case 'getAll':
            // Get all packaging records with product names
            $sql = "SELECT p.*, pr.productName as product_name 
                   FROM packaging p 
                   JOIN products pr ON p.batch_id = pr.productID 
                   ORDER BY p.created_at DESC";
            $result = $conn->query($sql);
            $packaging = [];
            while ($row = $result->fetch_assoc()) {
                $packaging[] = $row;
            }
            echo json_encode($packaging);
            break;

        case 'get':
            // Get specific packaging record
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $sql = "SELECT * FROM packaging WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
            break;
    }
}

// Handle POST request for creating/updating packaging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if we're updating an existing record
    if (isset($data['id']) && $data['id'] > 0) {
        // Update existing record
        $sql = "UPDATE packaging 
                SET batch_id = ?, 
                    packaging_type = ?, 
                    expire_date = ?, 
                    storage_temp = ?, 
                    weight = ?, 
                    packaging_date = ?, 
                    notes = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssdssi",
            $data['batch_id'],
            $data['packaging_type'],
            $data['expire_date'],
            $data['storage_temp'],
            $data['weight'],
            $data['packaging_date'],
            $data['notes'],
            $data['id']
        );
    } else {
        // Insert new record
        $sql = "INSERT INTO packaging (batch_id, packaging_type, expire_date, storage_temp, weight, packaging_date, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssdss",
            $data['batch_id'],
            $data['packaging_type'],
            $data['expire_date'],
            $data['storage_temp'],
            $data['weight'],
            $data['packaging_date'],
            $data['notes']
        );
    }
    
    $success = $stmt->execute();
    
    if ($success) {
        // Get the updated/inserted record with product name
        $id = isset($data['id']) ? $data['id'] : $conn->insert_id;
        $sql = "SELECT p.*, pr.productName as product_name 
                FROM packaging p 
                JOIN products pr ON p.batch_id = pr.productID 
                WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $updatedRecord = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'data' => $updatedRecord
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $stmt->error
        ]);
    }
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $sql = "DELETE FROM packaging WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    $success = $stmt->execute();
    echo json_encode(['success' => $success]);
}

$conn->close();
?>
