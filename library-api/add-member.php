<?php
// Allow from any origin
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// OPTIONS කියන method එකත් මෙතනට එකතු කළා
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Preflight OPTIONS request එක හැසිරවීම (මේක අනිවාර්යයි)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
include_once 'db.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Check if all required data is present
if (
    !empty($data->firstName) &&
    !empty($data->lastName) &&
    !empty($data->nic) &&
    !empty($data->email) &&
    !empty($data->phone) &&
    !empty($data->birthday)
) {
    // Insert query with standard VALUES syntax
    $query = "INSERT INTO members (firstname, lastname, nic, email, phone, birthday, description) 
              VALUES (:firstname, :lastname, :nic, :email, :phone, :birthday, :description)";

    $stmt = $db->prepare($query);

    // Sanitize input
    $firstname = htmlspecialchars(strip_tags($data->firstName));
    $lastname = htmlspecialchars(strip_tags($data->lastName));
    $nic = htmlspecialchars(strip_tags($data->nic));
    $email = htmlspecialchars(strip_tags($data->email));
    $phone = htmlspecialchars(strip_tags($data->phone));
    $birthday = htmlspecialchars(strip_tags($data->birthday));
    $description = !empty($data->description) ? htmlspecialchars(strip_tags($data->description)) : null;

    // Bind parameters using bindValue for better null handling
    $stmt->bindValue(":firstname", $firstname);
    $stmt->bindValue(":lastname", $lastname);
    $stmt->bindValue(":nic", $nic);
    $stmt->bindValue(":email", $email);
    $stmt->bindValue(":phone", $phone);
    $stmt->bindValue(":birthday", $birthday);
    $stmt->bindValue(":description", $description);

    try {
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("success" => true, "message" => "Member added successfully!"));
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Unable to add member. Database error."));
        }
    } catch (Exception $e) {
        // Handle duplicate NIC or Email errors, or any other DB errors gracefully
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Error: " . $e->getMessage()));
    }
} else {
    // Data is incomplete
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Please fill all required fields!"));
}
?>
