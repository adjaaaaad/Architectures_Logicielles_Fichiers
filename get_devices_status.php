<?php
/**
 * get_devices_status.php
 * Retourne le statut temps réel de chaque dispositif IoT (ESP32 / serrure).
 * Accès réservé aux administrateurs.
 *
 * Seuils :
 *   online  → last_seen < 30 s
 *   warning → last_seen entre 30 s et 300 s (5 min)
 *   offline → last_seen > 300 s ou jamais vu
 */
header('Content-Type: application/json');
require "config_db.php";
require "auth_middleware.php";
require_admin();

$conn = get_db();

// ── Requête : jointure serrures ↔ salles, tri par numéro de salle ─────────
$res = $conn->query("
    SELECT
        salles.id           AS salle_id,
        salles.numero_salle,
        serrures.id         AS serrure_id,
        serrures.last_seen,
        TIMESTAMPDIFF(SECOND, serrures.last_seen, NOW()) AS diff_sec
    FROM serrures
    JOIN salles ON serrures.salle_id = salles.id
    ORDER BY salles.numero_salle ASC
");

if (!$res) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur base de données : " . $conn->error]);
    exit;
}

$data = [];

while ($row = $res->fetch_assoc()) {
    $diff = (int) $row['diff_sec'];

    // Trois états avec seuils distincts
    if ($row['last_seen'] === null || $diff > 300) {
        $status = 'offline';
    } elseif ($diff > 30) {
        $status = 'warning';
    } else {
        $status = 'online';
    }

    $data[] = [
        'salle_id'      => (int) $row['salle_id'],
        'numero_salle'  => (int) $row['numero_salle'],
        'serrure_id'    => (int) $row['serrure_id'],
        'last_seen'     => $row['last_seen'],        // ISO datetime string
        'diff_sec'      => $diff,
        'status'        => $status,
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
