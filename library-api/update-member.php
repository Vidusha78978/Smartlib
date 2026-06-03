<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->firstName) && !empty($data->nic)) {
    try {
        $stmt = $db->prepare("UPDATE members SET firstname=:f, lastname=:l, nic=:n, email=:e, phone=:p, birthday=:b WHERE id=:id");
        $stmt->execute([
            ':f'=>$data->firstName, ':l'=>$data->lastName, ':n'=>$data->nic, 
            ':e'=>$data->email, ':p'=>$data->phone, ':b'=>$data->birthday, ':id'=>$data->id
        ]);
        echo json_encode(["success" => true]);
    } catch(Exception $e) { echo json_encode(["success" => false, "message" => $e->getMessage()]); }
}
?>
