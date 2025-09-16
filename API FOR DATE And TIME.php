//API FOR DATE AND TIME
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration (adjust these values)
$host = 'localhost';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Route handling
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch($method) {
    case 'GET':
        getCurrentDateTime();
        break;
    case 'POST':
        createDateTimeRecord();
        break;
    case 'PUT':
        updateDateTime();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function getCurrentDateTime() {
    global $pdo;
    
    try {
        // Get current server time
        $currentTime = date('Y-m-d H:i:s');
        
        // If you want to get from database
        $stmt = $pdo->query("SELECT id, datetime_field, updated_at FROM datetime_records ORDER BY updated_at DESC LIMIT 1");
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response = [
            'success' => true,
            'current_server_time' => $currentTime,
            'latest_record' => $record
        ];
        
        echo json_encode($response);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function createDateTimeRecord() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['datetime'])) {
        http_response_code(400);
        echo json_encode(['error' => 'datetime field is required']);
        return;
    }
    
    // Validate datetime format
    $datetime = $input['datetime'];
    if (!validateDateTime($datetime)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid datetime format. Use YYYY-MM-DD HH:MM:SS']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO datetime_records (datetime_field, created_at, updated_at) VALUES (?, NOW(), NOW())");
        $stmt->execute([$datetime]);
        
        $id = $pdo->lastInsertId();
        
        $response = [
            'success' => true,
            'message' => 'DateTime record created successfully',
            'id' => $id,
            'datetime' => $datetime
        ];
        
        http_response_code(201);
        echo json_encode($response);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function updateDateTime() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($input['id']) || !isset($input['datetime'])) {
        http_response_code(400);
        echo json_encode(['error' => 'id and datetime fields are required']);
        return;
    }
    
    $id = $input['id'];
    $datetime = $input['datetime'];
    
    // Validate datetime format
    if (!validateDateTime($datetime)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid datetime format. Use YYYY-MM-DD HH:MM:SS']);
        return;
    }
    
    try {
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM datetime_records WHERE id = ?");
        $stmt->execute([$id]);
        
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Record not found']);
            return;
        }
        
        // Update the record
        $stmt = $pdo->prepare("UPDATE datetime_records SET datetime_field = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$datetime, $id]);
        
        $response = [
            'success' => true,
            'message' => 'DateTime updated successfully',
            'id' => $id,
            'datetime' => $datetime,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function validateDateTime($datetime) {
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $d && $d->format('Y-m-d H:i:s') === $datetime;
}

// Alternative function to update current system time (for demo purposes)
function updateSystemDateTime() {
    // Note: This won't actually change system time, just for demonstration
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['datetime'])) {
        http_response_code(400);
        echo json_encode(['error' => 'datetime field is required']);
        return;
    }
    
    $datetime = $input['datetime'];
    
    if (!validateDateTime($datetime)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid datetime format']);
        return;
    }
    
    // In a real scenario, you might store this in a config file or database
    // Actually changing system time requires admin privileges and is not recommended via web API
    
    $response = [
        'success' => true,
        'message' => 'DateTime setting updated',
        'requested_datetime' => $datetime,
        'note' => 'System time cannot be changed via web API for security reasons'
    ];
    
    echo json_encode($response);
}
?>