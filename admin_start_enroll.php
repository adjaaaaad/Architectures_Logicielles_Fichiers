<?php
header('Content-Type: application/json');
require "config_db.php";
require "auth_middleware.php";
require_admin();

$conn = get_db();


if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
    echo json_encode(["success"=>false, "message"=>"Utilisateur manquant"]);
    exit;
}

$user_id = (int)$_POST['user_id'];

$check = $conn->query("SELECT id FROM users WHERE id=$user_id AND role='user'");
if($check->num_rows === 0){
    echo json_encode(["success"=>false, "message"=>"Utilisateur non trouvé"]);
    exit;
}

$conn->query("DELETE FROM enrollments WHERE status='waiting'");

$result = $conn->query("INSERT INTO enrollments (user_id, status) VALUES ($user_id, 'waiting')");

if($result){
    echo json_encode(["success"=>true, "message"=>"Enrôlement démarré"]);
} else {
    echo json_encode(["success"=>false, "message"=>"Erreur base de données: ".$conn->error]);
}
?>

