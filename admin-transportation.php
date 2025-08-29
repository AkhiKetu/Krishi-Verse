<?php
header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'krishi-verse';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'DB connection failed']);
    exit;
}

// Warehouses list
if (isset($_GET['warehouses']) && $_GET['warehouses']==1) {
    $warehouses = [];
    $result = $conn->query("SELECT name FROM warehouses ORDER BY name");
    while ($row = $result->fetch_assoc()) $warehouses[] = $row['name'];
    echo json_encode(['success'=>true,'warehouses'=>$warehouses]);
    exit;
}

// Products list
if (isset($_GET['products']) && $_GET['products']==1) {
    $products = [];
    $result = $conn->query("SELECT productName FROM products ORDER BY productName");
    while ($row = $result->fetch_assoc()) $products[] = $row['productName'];
    echo json_encode(['success'=>true,'products'=>$products]);
    exit;
}

// Fetch all transports
function getTransportAndStats($conn){
    $transports = [];
    $result = $conn->query("SELECT * FROM transportation ORDER BY created_at DESC");
    while($row = $result->fetch_assoc()){
        $transports[] = [
            'id'=>$row['id'],
            'origin'=>$row['origin'],
            'destination'=>$row['destination'],
            'product'=>$row['product'],
            'weight'=>$row['weight'],
            'distance'=>(float)$row['distance'],
            'temperature'=>$row['temperature'],
            'humidity'=>$row['humidity'],
            'vehicle'=>$row['vehicle'],
            'driver'=>$row['driver'],
            'schedule_date'=>$row['schedule_date'],
            'schedule_time'=>$row['schedule_time'],
            'priority'=>$row['priority'],
            'status'=>$row['status'],
            'costing'=>(float)$row['costing']
        ];
    }
    return ['success'=>true,'transports'=>$transports];
}

// DELETE
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='delete'){
    if(empty($_POST['id'])){ echo json_encode(['success'=>false,'message'=>'Missing id']); exit;}
    $id=(int)$_POST['id'];
    $stmt=$conn->prepare("DELETE FROM transportation WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(getTransportAndStats($conn));
    exit;
}

// UPDATE
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='update'){
    $id = (int)$_POST['id'];
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $product = $_POST['product'];
    $weight = $_POST['weight'];
    $distance = (float)$_POST['distance'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $vehicle = $_POST['vehicle'];
    $driver = $_POST['driver'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $priority = $_POST['priority'];
    $status='Scheduled';
    $costing = $distance*15;

    // FIXED bind_param types for status
    $stmt = $conn->prepare("UPDATE transportation SET origin=?, destination=?, product=?, weight=?, distance=?, temperature=?, humidity=?, vehicle=?, driver=?, schedule_date=?, schedule_time=?, priority=?, status=?, costing=? WHERE id=?");
    $stmt->bind_param("sssssdssssssssi",$origin,$destination,$product,$weight,$distance,$temperature,$humidity,$vehicle,$driver,$date,$time,$priority,$status,$costing,$id);
    if(!$stmt->execute()){ echo json_encode(['success'=>false,'message'=>'Failed to update']); exit;}
    $stmt->close();
    echo json_encode(getTransportAndStats($conn));
    exit;
}

// ADD
if($_SERVER['REQUEST_METHOD']==='POST' && (!isset($_POST['action']) || $_POST['action']!=='update' && $_POST['action']!=='delete')){
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $product = $_POST['product'];
    $weight = $_POST['weight'];
    $distance = (float)$_POST['distance'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $vehicle = $_POST['vehicle'];
    $driver = $_POST['driver'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $priority = $_POST['priority'];
    $status='Scheduled';
    $costing = $distance*15;

    $stmt = $conn->prepare("INSERT INTO transportation (origin,destination,product,weight,distance,temperature,humidity,vehicle,driver,schedule_date,schedule_time,priority,status,costing) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssdsssssssd",$origin,$destination,$product,$weight,$distance,$temperature,$humidity,$vehicle,$driver,$date,$time,$priority,$status,$costing);
    if(!$stmt->execute()){ echo json_encode(['success'=>false,'message'=>'Failed to add']); exit;}
    $stmt->close();
    echo json_encode(getTransportAndStats($conn));
    exit;
}

// GET transports
echo json_encode(getTransportAndStats($conn));
exit;
?>