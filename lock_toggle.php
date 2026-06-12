<?php
/**
 * lock_toggle.php
 * Ouvre ou ferme une serrure après vérification d'accès.
 * GET : user_id, numero (salle), action (open|close)
 */

require "auth_middleware.php";
require "config_db.php";
api_require_auth();

header('Content-Type: application/json');

$user_id = (int)($_GET['user_id'] ?? 0);
$numero  = (int)($_GET['numero']  ?? 0);
$action  = trim($_GET['action']   ?? '');

if (!$user_id || !$numero || !in_array($action, ['open', 'close'], true)) {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

// Sécurité : l'user ne peut agir que pour lui-même (sauf admin)
if ($_SESSION['role'] !== 'admin' && $user_id !== (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

$conn = get_db();

// Vérifier que l'utilisateur a accès à cette salle
$stmt = $conn->prepare(
    "SELECT salles.id AS salle_id
     FROM access
     JOIN salles ON access.salle_id = salles.id
     WHERE access.user_id = ? AND salles.numero_salle = ?
     LIMIT 1"
);
$stmt->bind_param('ii', $user_id, $numero);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Accès refusé à cette salle']);
    exit;
}

$salle_id = $res->fetch_assoc()['salle_id'];
$stmt->close();

// Mettre à jour l'état
$etat = ($action === 'open') ? 1 : 0;
$upd  = $conn->prepare('UPDATE serrures SET etat = ? WHERE salle_id = ?');
$upd->bind_param('ii', $etat, $salle_id);
$upd->execute();
$upd->close();

// Enregistrer dans les logs
$log = $conn->prepare('INSERT INTO logs (user_id, salle_id, action) VALUES (?, ?, ?)');
$log->bind_param('iis', $user_id, $salle_id, $action);
$log->execute();
$log->close();
$conn->close();

echo json_encode([
    'success' => true,
    'action'  => $action === 'open' ? 'ouverte' : 'fermée',
]);
