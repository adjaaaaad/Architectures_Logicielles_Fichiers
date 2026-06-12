<?php
$host = "mysql-kinesis.alwaysdata.net";
$dbname = "kinesis_bd";
$user = "kinesis";
$pass = "gpr-32026";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Erreur connexion DB"]));
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$salle = isset($_GET['numero']) ? intval($_GET['numero']) : null;
$action = isset($_GET['action']) ? strtolower(trim($_GET['action'])) : null;

if (!$user_id || !$salle || !in_array($action, ['open', 'close'])) {
    echo json_encode(["success" => false, "message" => "Paramètres invalides"]);
    $conn->close();
    exit;
}

// Vérifier accès user->salle
$sqlCheck = "SELECT access.id, salles.id AS salle_id
FROM access
JOIN salles ON access.salle_id = salles.id
WHERE access.user_id = $user_id
AND salles.numero_salle = $salle";

$res = $conn->query($sqlCheck);

if (!$res || $res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    $conn->close();
    exit;
}

$row = $res->fetch_assoc();
$salle_id = intval($row['salle_id']);

$etat = $action === 'open' ? 1 : 0;
$actionLabel = $action === 'open' ? 'ouverte' : 'fermée';

$update = $conn->query("UPDATE serrures SET etat = $etat WHERE salle_id = $salle_id");

if (!$update) {
    echo json_encode(["success" => false, "message" => "Échec de mise à jour de l'état"]);
    $conn->close();
    exit;
}

$log = $conn->query("INSERT INTO logs (user_id, salle_id, action, date_action) VALUES ($user_id, $salle_id, '$actionLabel', NOW())");

if (!$log) {
    echo json_encode(["success" => false, "message" => "Échec enregistrement log"]);
    $conn->close();
    exit;
}

echo json_encode(["success" => true, "message" => "Salle $actionLabel"]);

$conn->close();
?>