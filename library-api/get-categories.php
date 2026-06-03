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
    // SQL query to fetch all categories from the database (description removed)
    $sql = "SELECT id, category_name FROM categories ORDER BY id DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $categories = array();
    
    // Fetch rows one by one and push to the categories array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[] = $row;
    }
    
    // Send successful JSON response to React
    http_response_code(200);
    echo json_encode(array("success" => true, "categories" => $categories));
    
} catch (Exception $e) {
    // Handle any database errors gracefully
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Database Error: " . $e->getMessage()));
}
?>
