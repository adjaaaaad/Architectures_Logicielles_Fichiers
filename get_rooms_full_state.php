<?php
/**
 * get_rooms_full_state.php
 * Retourne l'état complet de chaque salle en une seule requête :
 *   - État serrure (etat : 0 = fermée, 1 = ouverte)
 *   - Mode courant (access | enroll)
 *   - Connectivité dispositif (online | warning | offline)
 *   - Dernier signal (last_seen, diff_sec)
 *
 * Seuils connectivité :
 *   online  → diff_sec ≤ 30 s
 *   warning → 30 s < diff_sec ≤ 300 s (5 min)
 *   offline → diff_sec > 300 s ou jamais vu
 *
 * Accès : administrateurs uniquement.
 */
header('Content-Type: application/json; charset=utf-8');
require "config_db.php";
require "auth_middleware.php";
require_admin();

$conn = get_db();

$res = $conn->query("
    SELECT
        salles.id               AS salle_id,
        salles.numero_salle,
        serrures.id             AS serrure_id,
        serrures.etat,
        serrures.mode,
        serrures.last_seen,
        TIMESTAMPDIFF(SECOND, serrures.last_seen, NOW()) AS diff_sec
    FROM serrures
    INNER JOIN salles ON salles.id = serrures.salle_id
    ORDER BY salles.numero_salle ASC
");

if (!$res) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur base de données : " . $conn->error]);
    exit;
}

$data = [];

while ($row = $res->fetch_assoc()) {
    $diff = ($row['last_seen'] !== null) ? (int)$row['diff_sec'] : null;

    // Calcul du statut de connectivité
    if ($diff === null || $diff > 300) {
        $device_status = 'offline';
    } elseif ($diff > 30) {
        $device_status = 'warning';
    } else {
        $device_status = 'online';
    }

    $data[] = [
        'salle_id'      => (int)$row['salle_id'],
        'numero_salle'  => (int)$row['numero_salle'],
        'serrure_id'    => (int)$row['serrure_id'],
        'etat'          => (int)$row['etat'],          // 0 = fermée, 1 = ouverte
        'mode'          => $row['mode'],               // 'access' | 'enroll'
        'last_seen'     => $row['last_seen'],
        'diff_sec'      => $diff,
        'device_status' => $device_status,
    ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
