<?php
// Allow requests from any origin (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

// Get posted data from React
$data = json_decode(file_get_contents("php://input"));

// Check if email and password are provided
if (!empty($data->email) && !empty($data->password)) {
    try {
        // Prepare SQL query to check if user exists
        $query = "SELECT id, email, password, role FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        
        // Bind email parameter
        $stmt->bindParam(":email", $data->email);
        $stmt->execute();
        
        // Fetch the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify if user exists and password matches
        // (Note: Using plain text password matching based on your database dump)
        if ($user && $user['password'] === $data->password) {
            // Login successful
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "Login successful.",
                "role" => $user['role']
            ));
        } else {
            // Invalid credentials
            http_response_code(401);
            echo json_encode(array(
                "success" => false,
                "message" => "Invalid email or password!"
            ));
        }
    } catch (Exception $e) {
        // Handle database errors
        http_response_code(500);
        echo json_encode(array(
            "success" => false,
            "message" => "Database Error: " . $e->getMessage()
        ));
    }
} else {
    // Data is incomplete
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Please provide both email and password."
    ));
}
?>
