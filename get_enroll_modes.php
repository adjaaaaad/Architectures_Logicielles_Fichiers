<?php
/**
 * get_enroll_modes.php
 * Retourne l'état du mode d'enrôlement pour toutes les serrures.
 * 
 * Réponse:
 *   [
 *     { "numero_salle": 101, "mode": "access" },
 *     { "numero_salle": 102, "mode": "enroll" },
 *     ...
 *   ]
 */

header('Content-Type: application/json');

try {
    require "config_db.php";
    $conn = get_db();

    $sql = "
        SELECT s.numero_salle, se.mode
        FROM salles s
        LEFT JOIN serrures se ON se.salle_id = s.id
        ORDER BY s.numero_salle ASC
    ";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Erreur SQL: " . $conn->error);
    }

    $modes = [];
    while ($row = $result->fetch_assoc()) {
        $modes[] = [
            'numero_salle' => (int)$row['numero_salle'],
            'mode' => $row['mode'] ?? 'access'
        ];
    }

    echo json_encode($modes);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
