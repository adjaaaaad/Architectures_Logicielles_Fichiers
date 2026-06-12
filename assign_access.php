<?php
/**
 * api/admin/assign_access.php
 * Attribue ou retire l'accès d'un utilisateur à une salle.
 * Réservé aux admins.
 *
 * POST params:
 *   user_id  : int
 *   salle_id : int
 *   action   : 'grant' | 'revoke'
 */

session_start();
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/db.php';

api_require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
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

// Vérifier que l'utilisateur et la salle existent
$chk = $conn->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$chk->bind_param('i', $user_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows === 0) {
    $chk->close(); $conn->close();
    echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
    exit;
}
$chk->close();

$chk2 = $conn->prepare('SELECT id FROM salles WHERE id = ? LIMIT 1');
$chk2->bind_param('i', $salle_id);
$chk2->execute();
$chk2->store_result();
if ($chk2->num_rows === 0) {
    $chk2->close(); $conn->close();
    echo json_encode(['success' => false, 'message' => 'Salle introuvable']);
    exit;
}
$chk2->close();

if ($action === 'grant') {
    // Insérer seulement si l'accès n'existe pas encore
    $stmt = $conn->prepare(
        'INSERT IGNORE INTO access (user_id, salle_id) VALUES (?, ?)'
    );
    $stmt->bind_param('ii', $user_id, $salle_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => $affected > 0 ? 'Accès accordé' : 'Accès déjà existant',
    ]);

} else {
    // Révoquer l'accès
    $stmt = $conn->prepare(
        'DELETE FROM access WHERE user_id = ? AND salle_id = ?'
    );
    $stmt->bind_param('ii', $user_id, $salle_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => $affected > 0 ? 'Accès révoqué' : 'Accès inexistant',
    ]);
}
