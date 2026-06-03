<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->name) && !empty($data->barcode)) {
    try {
        $stmt = $db->prepare("UPDATE books SET name=:n, author=:a, category_id=:c, barcode=:b, quantity=:q WHERE id=:id");
        $stmt->execute([
            ':n'=>$data->name, ':a'=>$data->author, ':c'=>$data->category_id, 
            ':b'=>$data->barcode, ':q'=>$data->quantity, ':id'=>$data->id
        ]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) { echo json_encode(["success" => false, "message" => $e->getMessage()]); }
} else { echo json_encode(["success" => false, "message" => "Incomplete data"]); }
?>
