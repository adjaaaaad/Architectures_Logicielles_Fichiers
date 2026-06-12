<?php
/**
 * admin_assign_access.php
 * Accorde ou révoque l'accès d'un utilisateur à une salle. Admin seulement.
 * POST : user_id, salle_id, action (grant|revoke)
 */

require "auth_middleware.php";
require "config_db.php";
api_require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$user_id  = (int)($_POST['user_id']  ?? 0);
$salle_id = (int)($_POST['salle_id'] ?? 0);
$action   = trim($_POST['action']    ?? '');

if (!$user_id || !$salle_id || !in_array($action, ['grant', 'revoke'], true)) {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

$conn = get_db();

if ($action === 'grant') {
    $stmt = $conn->prepare('INSERT IGNORE INTO access (user_id, salle_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $user_id, $salle_id);
    $stmt->execute();
    $msg = $stmt->affected_rows > 0 ? 'Accès accordé' : 'Accès déjà existant';
} else {
    $stmt = $conn->prepare('DELETE FROM access WHERE user_id = ? AND salle_id = ?');
    $stmt->bind_param('ii', $user_id, $salle_id);
    $stmt->execute();
    $msg = $stmt->affected_rows > 0 ? 'Accès révoqué' : 'Accès inexistant';
}

$stmt->close();
$conn->close();
echo json_encode(['success' => true, 'message' => $msg]);
