<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();

try {
    $db->exec("TRUNCATE TABLE logs");
    echo json_encode(["success" => true]);
} catch(Exception $e) { echo json_encode(["success" => false, "message" => $e->getMessage()]); }
?>
