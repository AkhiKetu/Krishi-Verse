<?php
header('Content-Type: application/json');

// DB config file
// Database implementation
// individual config
// For configaration 
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

// Helper: fetch all documents and stats
//helper module 
// MODULE

function getDocumentsAndStats($conn) {
    $docs = [];
    $result = $conn->query("SELECT * FROM documents ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()) {
        $docs[] = [
            'id' => $row['id'],
            'type' => $row['doc_type'],
            'number' => $row['doc_number'],
            'shipmentId' => $row['shipment_id'],
            'productName' => $row['product_name'],
            'distribution' => $row['distribution_center'],
            'issueDate' => $row['issue_date'],
            'expiryDate' => $row['expiry_date'],
            'status' => $row['status'],
            'compliance' => $row['compliance'],
            'fileSize' => $row['file_size'],
        ];
    }
    // Stats stat per compliant
    // Compliant statement
    $total = (int)$conn->query("SELECT COUNT(*) FROM documents")->fetch_row()[0];
    $pending = (int)$conn->query("SELECT COUNT(*) FROM documents WHERE status='Pending Review'")->fetch_row()[0];
    $compliantCount = (int)$conn->query("SELECT COUNT(*) FROM documents WHERE compliance='Yes'")->fetch_row()[0];
    $expiringSoon = (int)$conn->query("SELECT COUNT(*) FROM documents WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetch_row()[0];

    $complianceRate = $total > 0 ? round(($compliantCount / $total) * 100) : 0;

    return [
        'success' => true,
        'documents' => $docs,
        'stats' => [
            'total' => $total,
            'pending' => $pending,
            'complianceRate' => $complianceRate,
            'expiringSoon' => $expiringSoon,
        ]
    ];
}

// Handle "Mark Reviewed" update
// Mark Reviewed
// new update here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'markReviewed') {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid document ID']);
        exit;
    }
    $docId = (int)$_POST['id'];

    $stmt = $conn->prepare("UPDATE documents SET status = 'Reviewed', compliance = 'Yes' WHERE id = ?");
    $stmt->bind_param("i", $docId);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update document']);
        exit;
    }

    $stmt->close();

    // Return updated stats only
    // UPDATE MODAL
    $stats = getDocumentsAndStats($conn)['stats'];

    echo json_encode(['success' => true, 'stats' => $stats]);
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new document
    // For new additions

    // Validate required POST fields
    // Validation requirements
    $required = ['docType', 'docNumber', 'shipmentId', 'productName', 'distribution', 'issueDate', 'expiryDate', 'fileSize'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
            exit;
        }
    }

    $docType = $conn->real_escape_string(trim($_POST['docType']));
    $docNumber = $conn->real_escape_string(trim($_POST['docNumber']));
    $shipmentId = $conn->real_escape_string(trim($_POST['shipmentId']));
    $productName = $conn->real_escape_string(trim($_POST['productName']));
    $distribution = $conn->real_escape_string(trim($_POST['distribution']));
    $issueDate = $conn->real_escape_string($_POST['issueDate']);
    $expiryDate = $conn->real_escape_string($_POST['expiryDate']);
    $fileSize = $conn->real_escape_string($_POST['fileSize']);

    // Set default status and compliance for new docs
    // For compliance
    $status = 'Pending Review';
    $compliance = 'No';

    $stmt = $conn->prepare("INSERT INTO documents (doc_type, doc_number, shipment_id, product_name, distribution_center, issue_date, expiry_date, status, compliance, file_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $docType, $docNumber, $shipmentId, $productName, $distribution, $issueDate, $expiryDate, $status, $compliance, $fileSize);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add document']);
        exit;
    }

    $stmt->close();

    echo json_encode(getDocumentsAndStats($conn));
    $conn->close();
    exit;
}

// For GET, just return documents + stats
//  return documents + stats
echo json_encode(getDocumentsAndStats($conn));
$conn->close();
exit;
// For GET, just return documents + stats
// For GET, just return documents + stats

