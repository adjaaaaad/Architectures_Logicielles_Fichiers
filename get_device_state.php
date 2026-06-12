<?php

require "config_db.php";

$conn = get_db();

if(!isset($_GET['numero'])){

    echo json_encode([
        "success" => false,
        "message" => "Numero manquant"
    ]);

    exit;
}

$numero = $_GET['numero'];

$sql = "
SELECT 
    serrures.etat,
    serrures.mode
FROM serrures
JOIN salles ON salles.id = serrures.salle_id
WHERE salles.numero_salle = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $numero);

$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows > 0){

    $row = $res->fetch_assoc();

    echo json_encode([
        "success" => true,
        "etat_serrure" => intval($row['etat']),
        "mode" => $row['mode']
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Serrure introuvable"
    ]);
}

$stmt->close();
$conn->close();

?>