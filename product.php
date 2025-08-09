<?php
header('Content-Type: application/json');

// DB connection info
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get action param
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'list':
            $stmt = $pdo->query("SELECT * FROM products ORDER BY productID ASC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($products);
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            if (!$id) {
                echo json_encode(['error' => 'No product ID provided']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM products WHERE productID = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($product ?: []);
            break;

        case 'save':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'No data provided']);
                exit;
            }

            // Validate required fields here if needed

            // Check if product exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE productID = ?");
            $stmt->execute([$data['productID']]);
            $exists = $stmt->fetchColumn() > 0;

            if ($exists) {
                // Update existing
                $sql = "UPDATE products SET
                    productName = :productName,
                    category = :category,
                    price = :price,
                    packaging = :packaging,
                    supplier = :supplier,
                    status = :status,
                    plantingDate = :plantingDate,
                    harvestDate = :harvestDate,
                    shelfLife = :shelfLife,
                    storageTemp = :storageTemp,
                    humidity = :humidity
                    WHERE productID = :productID";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':productID' => $data['productID'],
                    ':productName' => $data['productName'],
                    ':category' => $data['category'],
                    ':price' => $data['price'],
                    ':packaging' => $data['packaging'],
                    ':supplier' => $data['supplier'],
                    ':status' => $data['status'],
                    ':plantingDate' => $data['plantingDate'],
                    ':harvestDate' => $data['harvestDate'],
                    ':shelfLife' => $data['shelfLife'],
                    ':storageTemp' => $data['storageTemp'],
                    ':humidity' => $data['humidity'],
                ]);
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                // Insert new
                $sql = "INSERT INTO products (
                    productID, productName, category, price, packaging,
                    supplier, status, plantingDate, harvestDate,
                    shelfLife, storageTemp, humidity
                ) VALUES (
                    :productID, :productName, :category, :price, :packaging,
                    :supplier, :status, :plantingDate, :harvestDate,
                    :shelfLife, :storageTemp, :humidity
                )";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':productID' => $data['productID'],
                    ':productName' => $data['productName'],
                    ':category' => $data['category'],
                    ':price' => $data['price'],
                    ':packaging' => $data['packaging'],
                    ':supplier' => $data['supplier'],
                    ':status' => $data['status'],
                    ':plantingDate' => $data['plantingDate'],
                    ':harvestDate' => $data['harvestDate'],
                    ':shelfLife' => $data['shelfLife'],
                    ':storageTemp' => $data['storageTemp'],
                    ':humidity' => $data['humidity'],
                ]);
                echo json_encode(['success' => true, 'message' => 'Product added successfully']);
            }
            break;

        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['productID'] ?? '';
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'No product ID provided']);
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM products WHERE productID = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
