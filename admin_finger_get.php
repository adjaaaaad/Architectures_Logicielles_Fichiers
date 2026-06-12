<?php
/**
 * admin_finger_get.php
 * Retourne la liste de toutes les empreintes enregistrées avec les infos utilisateur.
 *
 * Usage : GET admin_finger_get.php
 *
 * Réponse : tableau JSON
 * [
 *   {
 *     "id": 1,
 *     "fingerprint_id": 3,
 *     "utilisateur_id": 12,
 *     "nom": "Jean Dupont",
 *     "date_creation": "2025-06-01 14:32:00"
 *   },
 *   ...
 * ]
 */
header("Content-Type: application/json; charset=utf-8");
require "auth_middleware.php";
require_admin();
require "config_db.php";

$conn = get_db();

$result = $conn->query(
    "SELECT
        e.id,
        e.fingerprint_id,
        e.utilisateur_id,
        u.nom,
        e.date_creation
     FROM empreintes e
     JOIN users u ON u.id = e.utilisateur_id
     ORDER BY e.date_creation DESC"
);

if (!$result) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de requête"]);
    $conn->close();
    exit;
}

$empreintes = [];
while ($row = $result->fetch_assoc()) {
    $empreintes[] = [
        "id"             => (int)$row['id'],
        "fingerprint_id" => (int)$row['fingerprint_id'],
        "utilisateur_id" => (int)$row['utilisateur_id'],
        "nom"            => $row['nom'],
        "date_creation"  => $row['date_creation']
    ];
}

echo json_encode($empreintes);
$conn->close();
?>
