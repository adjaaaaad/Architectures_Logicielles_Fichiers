<?php
header('Content-Type: application/json');
require "config_db.php";

$conn = get_db();

// Vérifier s'il y a un enrôlement en attente
$res = $conn->query("SELECT id FROM enrollments WHERE status='waiting' LIMIT 1");

if($res && $res->num_rows > 0){
    echo json_encode(["enroll"=>true]);
} else {
    echo json_encode(["enroll"=>false]);
}
?>
