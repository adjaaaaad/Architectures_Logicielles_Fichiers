<?php
/**
 * log_get.php
 * Retourne les logs d'accès (JSON).
 * ?user_id=X → filtre sur un utilisateur précis (optionnel)
 */

require "auth_middleware.php";
require "config_db.php";
api_require_auth();

header('Content-Type: application/json');

$conn = get_db();

// Si user_id passé en GET, filtrer (utilisé par user_dashboard)
if (!empty($_GET['user_id'])) {
    $uid  = (int)$_GET['user_id'];
    // Sécurité : un user ne peut lire que ses propres logs
    if ($_SESSION['role'] !== 'admin' && $uid !== (int)$_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Accès refusé']);
        exit;
    }
    $stmt = $conn->prepare(
        "SELECT logs.*, users.nom, salles.numero_salle
         FROM logs
         JOIN users  ON logs.user_id  = users.id
         JOIN salles ON logs.salle_id = salles.id
         WHERE logs.user_id = ?
         ORDER BY date_action DESC
         LIMIT 50"
    );
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    // Tous les logs — admin seulement
    api_require_admin();
    $res = $conn->query(
        "SELECT logs.*, users.nom, salles.numero_salle
         FROM logs
         JOIN users  ON logs.user_id  = users.id
         JOIN salles ON logs.salle_id = salles.id
         ORDER BY date_action DESC
         LIMIT 100"
    );
}

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
$conn->close();
echo json_encode($data);
