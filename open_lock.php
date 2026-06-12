<?php
$host = "mysql-kinesis.alwaysdata.net";
$dbname = "kinesis_bd";
$user = "kinesis";
$pass = "gpr-32026";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur connexion DB");
}


$user_id = $_GET['user_id'];
$salle = $_GET['numero'];

// vérifier accès
$sqlCheck = "SELECT access.id, salles.id as salle_id
FROM access
JOIN salles ON access.salle_id = salles.id
WHERE access.user_id = '$user_id'
AND salles.numero_salle = '$salle'";

$res = $conn->query($sqlCheck);

if($res->num_rows > 0){

$row = $res->fetch_assoc();
$salle_id = $row['salle_id'];

// ouvrir serrure
$conn->query("UPDATE serrures SET etat = 1 WHERE salle_id = $salle_id");

// log
$conn->query("INSERT INTO logs (user_id, salle_id, action)
VALUES ('$user_id', '$salle_id', 'ouverte')");

echo json_encode(["success"=>true]);

} else {

echo json_encode(["success"=>false, "message"=>"Accès refusé"]);

}

$conn->close();
?>