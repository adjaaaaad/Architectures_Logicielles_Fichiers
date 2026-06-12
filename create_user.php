<?php
/**
 * api/admin/create_user.php
 * Crée un nouvel utilisateur (admin ou user).
 * Réservé aux admins.
 */

session_start();
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/db.php';

api_require_admin();

header('Content-Type: application/json');

// Accepter uniquement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$nom  = trim($_POST['nom']  ?? '');
$pin  = trim($_POST['pin']  ?? '');
$role = trim($_POST['role'] ?? 'user');

// Validation
if (empty($nom) || empty($pin)) {
    echo json_encode(['success' => false, 'message' => 'Nom et PIN requis']);
    exit;
}

if (!in_array($role, ['admin', 'user'], true)) {
    echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
    exit;
}

if (strlen($pin) < 4) {
    echo json_encode(['success' => false, 'message' => 'Le PIN doit faire au moins 4 caractères']);
    exit;
}

$conn = get_db();

// Vérifier si le nom existe déjà
$check = $conn->prepare('SELECT id FROM users WHERE nom = ? LIMIT 1');
$check->bind_param('s', $nom);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => "L'utilisateur \"$nom\" existe déjà"]);
    exit;
}
$check->close();

// Insertion
$stmt = $conn->prepare('INSERT INTO users (nom, pin, role) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $nom, $pin, $role);

if ($stmt->execute()) {
    $new_id = $conn->insert_id;
    $stmt->close();
    $conn->close();
    echo json_encode([
        'success' => true,
        'message' => "Utilisateur \"$nom\" créé avec succès",
        'user_id' => $new_id,
    ]);
} else {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
}
