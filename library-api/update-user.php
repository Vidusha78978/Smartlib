<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->email) && !empty($data->role)) {
    try {
        if(!empty($data->password)) {
            // update with password
            $stmt = $db->prepare("UPDATE users SET email=:e, role=:r, password=:p WHERE id=:id");
            $stmt->execute([':e'=>$data->email, ':r'=>$data->role, ':p'=>$data->password, ':id'=>$data->id]);
        } else {
            // Update without password
            $stmt = $db->prepare("UPDATE users SET email=:e, role=:r WHERE id=:id");
            $stmt->execute([':e'=>$data->email, ':r'=>$data->role, ':id'=>$data->id]);
        }
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Incomplete data provided."]);
}
?>
