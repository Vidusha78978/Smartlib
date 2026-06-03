<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT id, action, description, performed_by as performedBy, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') as date FROM logs ORDER BY id DESC");
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(["success" => true, "logs" => $logs]);
?>
