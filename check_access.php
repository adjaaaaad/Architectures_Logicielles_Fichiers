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

$sql = "SELECT access.id
FROM access
JOIN salles ON access.salle_id = salles.id
WHERE access.user_id = '$user_id'
AND salles.numero_salle = '$salle'";

$result = $conn->query($sql);

echo json_encode([
    "access" => $result->num_rows > 0
]);

$conn->close();
?>