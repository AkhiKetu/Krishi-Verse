<?php
header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'DB Connection Failed']));
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch products for dropdown
    if (isset($_GET['products'])) {
        $result = $conn->query("SELECT productID, productName FROM products");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode(['success' => true, 'products' => $products]);
        exit;
    }

    // Fetch warehouses for dropdown
    if (isset($_GET['warehouses'])) {
        $result = $conn->query("SELECT id, name FROM warehouses");
        $warehouses = [];
        while ($row = $result->fetch_assoc()) {
            $warehouses[] = $row;
        }
        echo json_encode(['success' => true, 'warehouses' => $warehouses]);
        exit;
    }

    // Fetch inventory list
    $sql = "SELECT i.id, i.product_id, p.productName, i.main_storage, i.warehouse_id, w.name AS warehouseName, i.in_warehouse, i.last_available
            FROM inventory i
            JOIN products p ON i.product_id = p.productID
            JOIN warehouses w ON i.warehouse_id = w.id
            ORDER BY i.id DESC";
    $result = $conn->query($sql);
    $inventory = [];
    while ($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
    echo json_encode(['success' => true, 'inventory' => $inventory]);
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    // Add or update inventory
    if (isset($data['action']) && $data['action'] === 'save') {
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $product_id = $conn->real_escape_string($data['product_id']);
        $warehouse_id = intval($data['warehouse_id']);
        $main_storage = floatval($data['main_storage']);
        $in_warehouse = floatval($data['in_warehouse']);
        $last_available = floatval($data['last_available']);

        if ($id > 0) {
            // Update
            $sql = "UPDATE inventory SET 
                        product_id='$product_id', 
                        warehouse_id=$warehouse_id, 
                        main_storage=$main_storage, 
                        in_warehouse=$in_warehouse, 
                        last_available=$last_available 
                    WHERE id=$id";
            $msg = "Inventory updated successfully";
        } else {
            // Insert
            $sql = "INSERT INTO inventory (product_id, warehouse_id, main_storage, in_warehouse, last_available)
                    VALUES ('$product_id', $warehouse_id, $main_storage, $in_warehouse, $last_available)";
            $msg = "Inventory added successfully";
        }

        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        exit;
    }

    // Delete inventory
    if (isset($data['action']) && $data['action'] === 'delete') {
        $id = intval($data['id']);
        $sql = "DELETE FROM inventory WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Inventory deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        exit;
    }
}

$conn->close();
?>
