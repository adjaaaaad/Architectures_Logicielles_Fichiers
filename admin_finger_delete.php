<?php
/**
 * admin_finger_delete.php
 * Supprime une empreinte de la base de données.
 *
 * POST :
 *   id : ID de l'empreinte dans la table empreintes
 *
 * Note : ceci supprime uniquement l'association DB.
 * La suppression physique dans le capteur est effectuée par l'ESP32
 * lors de la prochaine synchronisation (mode sync) ou au réenrôlement.
 * Pour une suppression immédiate du capteur, l'ESP32 doit implémenter
 * un endpoint /finger_delete_from_sensor.php (hors scope de ce patch).
 */
header("Content-Type: application/json; charset=utf-8");
require "auth_middleware.php";
require_admin();
require "config_db.php";

$conn = get_db();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID invalide"]);
    exit;
}

// Récupérer les infos avant suppression (pour confirmation)
$stmt = $conn->prepare(
    "SELECT e.fingerprint_id, u.nom
     FROM empreintes e
     JOIN users u ON u.id = e.utilisateur_id
     WHERE e.id = ?"
);
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Empreinte introuvable"]);
    exit;
}
$data = $res->fetch_assoc();
$stmt->close();

// Supprimer l'empreinte
$stmt = $conn->prepare("DELETE FROM empreintes WHERE id = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Erreur de suppression"]);
    exit;
}
$stmt->close();

echo json_encode([
    "success"        => true,
    "message"        => "Empreinte #{$data['fingerprint_id']} de {$data['nom']} supprimée",
    "fingerprint_id" => (int)$data['fingerprint_id']
]);

$conn->close();

function _dbError(mysqli $conn): never {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur interne du serveur"]);
    $conn->close();
    exit;
}
?>
