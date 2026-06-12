<?php
require "config_db.php";

$conn = get_db();

$numero = $_GET['numero'] ?? '';

if($numero == ''){
    echo json_encode(["success"=>false,"msg"=>"no numero"]);
    exit;
}

// 🔹 récupérer salle
$res = $conn->query("SELECT id FROM salles WHERE numero_salle = '$numero'");

if($res->num_rows == 0){
    echo json_encode(["success"=>false,"msg"=>"salle not found"]);
    exit;
}

$salle = $res->fetch_assoc();
$salle_id = $salle['id'];

// 🔹 update
$sql = "UPDATE serrures SET last_seen = NOW() WHERE salle_id = $salle_id";

if($conn->query($sql)){
    echo json_encode(["success"=>true]);
} else {
    echo json_encode(["success"=>false,"error"=>$conn->error]);
}
?>