<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }
include_once 'db.php';
$db = (new Database())->getConnection();

try {
    $total_books = $db->query("SELECT SUM(quantity) as total FROM books")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $total_members = $db->query("SELECT COUNT(*) as total FROM members")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    echo json_encode([
        "success" => true, 
        "total_books" => (int)$total_books, 
        "total_members" => (int)$total_members, 
        "pending_returns" => 0 // Return feature එක පසුව add කළ විට මෙය වෙනස් කරන්න පුළුවන්
    ]);
} catch(Exception $e) { echo json_encode(["success" => false]); }
?>
