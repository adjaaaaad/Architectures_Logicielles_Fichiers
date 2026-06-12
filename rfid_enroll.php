<?php
header('Content-Type: application/json');
require "config_db.php";

$conn = get_db();
$uid = isset($_GET['uid']) ? strtoupper(trim($_GET['uid'])) : '';

if(empty($uid)){
    echo json_encode(["success"=>false, "message"=>"UID invalide"]);
    exit;
}

// Chercher enrôlement en attente
$res = $conn->query("SELECT * FROM enrollments WHERE status='waiting' ORDER BY id DESC LIMIT 1");

if($res->num_rows == 0){
    echo json_encode(["success"=>false, "message"=>"Aucun enrôlement en attente"]);
    exit;
}

$enroll = $res->fetch_assoc();
$user_id = (int)$enroll['user_id'];

// Vérifier que l'utilisateur existe
$user_check = $conn->query("SELECT id FROM users WHERE id=$user_id");
if($user_check->num_rows == 0){
    echo json_encode(["success"=>false, "message"=>"Utilisateur invalide"]);
    exit;
}

// Enregistrer l'UID du badge
$uid_escaped = $conn->real_escape_string($uid);
$update_result = $conn->query("UPDATE users SET rfid_uid='$uid_escaped' WHERE id=$user_id");

if(!$update_result){
    echo json_encode(["success"=>false, "message"=>"Erreur d'enregistrement"]);
    exit;
}

// Marquer l'enrôlement comme terminé
$enroll_id = (int)$enroll['id'];
$conn->query("UPDATE enrollments SET status='done' WHERE id=$enroll_id");

echo json_encode([
    "success"=>true,
    "message"=>"Badge enregistré avec succès"
]);
?>
