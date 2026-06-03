<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->barcode) && !empty($data->name) && !empty($data->author) && !empty($data->category_id)) {
    try {
        $stmt = $db->prepare("INSERT INTO books (barcode, name, author, category_id, quantity) VALUES (:barcode, :name, :author, :category_id, :quantity)");
        $stmt->execute([
            ':barcode' => $data->barcode, ':name' => $data->name, 
            ':author' => $data->author, ':category_id' => $data->category_id, 
            ':quantity' => $data->quantity ?? 1
        ]);
        echo json_encode(["success" => true, "message" => "Book added successfully!"]);
    } catch(Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else { echo json_encode(["success" => false, "message" => "Incomplete data"]); }
?>
