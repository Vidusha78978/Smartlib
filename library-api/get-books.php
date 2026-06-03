<?php
// Allow requests from any origin (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
    // SQL query to fetch books with their category names
    $sql = "SELECT b.id, b.barcode, b.name, b.author, c.category_name as category, b.category_id, b.quantity, b.created_at 
            FROM books b 
            LEFT JOIN categories c ON b.category_id = c.id 
            ORDER BY b.id DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $books = array();
    
    // Fetch rows one by one and push to the books array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $books[] = $row;
    }
    
    // Send successful JSON response to React
    echo json_encode(array("success" => true, "books" => $books));
    
} catch (Exception $e) {
    // Handle any database errors
    echo json_encode(array("success" => false, "message" => "Database Error: " . $e->getMessage()));
}
?>
