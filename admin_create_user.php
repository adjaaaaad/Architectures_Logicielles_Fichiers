<?php
/**
 * admin_create_user.php
 * Crée un nouvel utilisateur. Admin seulement.
 * POST : nom, pin, role
 */

require "auth_middleware.php";
require "config_db.php";
api_require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$nom  = trim($_POST['nom']  ?? '');
$pin  = trim($_POST['pin']  ?? '');
$role = trim($_POST['role'] ?? 'user');

if (empty($nom) || empty($pin)) {
    echo json_encode(['success' => false, 'message' => 'Nom et PIN requis']);
    exit;
}
if (strlen($pin) < 4) {
    echo json_encode(['success' => false, 'message' => 'PIN trop court (min 4 caractères)']);
    exit;
}
if (!in_array($role, ['admin', 'user'], true)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
    exit;
}

$conn = get_db();

// Vérifier unicité du nom
$chk = $conn->prepare('SELECT id FROM users WHERE nom = ? LIMIT 1');
$chk->bind_param('s', $nom);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close(); $conn->close();
    echo json_encode(['success' => false, 'message' => "Nom \"$nom\" déjà utilisé"]);
    exit;
}
$chk->close();

$stmt = $conn->prepare('INSERT INTO users (nom, pin, role) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $nom, $pin, $role);

if ($stmt->execute()) {
    $id = $conn->insert_id;
    $stmt->close(); $conn->close();
    echo json_encode(['success' => true, 'message' => "Utilisateur \"$nom\" créé", 'user_id' => $id]);
} else {
    $stmt->close(); $conn->close();
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
}
