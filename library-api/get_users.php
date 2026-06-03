<?php
// Allow requests from any origin (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection file
include_once 'db.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Fetch users from the database
    $query = "SELECT id, email, role, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as created_at FROM users ORDER BY id DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    // Fetch all users as an associative array
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Send successful response with users data
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "users" => $users
    ));
    
} catch (Exception $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Database Error: " . $e->getMessage()
    ));
}
?>
