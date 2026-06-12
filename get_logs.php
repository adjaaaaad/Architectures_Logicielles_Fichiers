<?php
require "config_db.php";

$conn = get_db();

if ($conn->connect_error) {
    die("Erreur connexion DB");
}


$sql = "SELECT logs.*, users.nom, salles.numero_salle
FROM logs
JOIN users ON logs.user_id = users.id
JOIN salles ON logs.salle_id = salles.id
ORDER BY date_action DESC
LIMIT 20";

$res = $conn->query($sql);

$data = [];

while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);

$conn->close();
?>