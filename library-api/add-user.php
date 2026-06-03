<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->email) && !empty($data->password) && !empty($data->role)) {
    try {
        $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
        $stmt->execute([':email' => $data->email, ':password' => $data->password, ':role' => $data->role]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) {
        // Handle duplicate emails
        echo json_encode(["success" => false, "message" => "Error: Email might already exist."]);
    }
} else { 
    echo json_encode(["success" => false, "message" => "Please fill all fields"]); 
}
?>
