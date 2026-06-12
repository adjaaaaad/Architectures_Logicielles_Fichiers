<?php

require "config_db.php";

$conn = get_db();

if(
    !isset($_GET['numero']) ||
    !isset($_GET['mode'])
){

    echo json_encode([
        "success" => false,
        "message" => "Parametres manquants"
    ]);

    exit;
}

$numero = $_GET['numero'];
$mode = $_GET['mode'];

$sql = "
UPDATE serrures
JOIN salles ON salles.id = serrures.salle_id
SET serrures.mode = ?
WHERE salles.numero_salle = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $mode, $numero);

if($stmt->execute()){

    echo json_encode([
        "success" => true,
        "message" => "Mode mis a jour"
    ]);

}else{

    echo json_encode([
        "success" => false,
        "message" => "Erreur SQL"
    ]);
}

$stmt->close();
$conn->close();

?>