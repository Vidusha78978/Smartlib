<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->action) && !empty($data->description) && !empty($data->performedBy)) {
    $stmt = $db->prepare("INSERT INTO logs (action, description, performed_by) VALUES (:a, :d, :p)");
    $stmt->execute([':a' => $data->action, ':d' => $data->description, ':p' => $data->performedBy]);
    echo json_encode(["success" => true]);
}
?>
