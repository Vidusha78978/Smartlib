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

// Include database connection
include_once 'db.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Fetch all members from the database
    // We use 'as' to match the camelCase property names expected by the React frontend (firstName, lastName)
    $query = "SELECT id, firstname as firstName, lastname as lastName, nic, email, phone, birthday, description FROM members ORDER BY id DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();

    $members = array();

    // Loop through the results and push each member into the array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($members, $row);
    }

    // Send a successful response with the members data
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "members" => $members
    ));

} catch (Exception $e) {
    // Handle database errors gracefully
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Database Error: " . $e->getMessage()
    ));
}
?>
