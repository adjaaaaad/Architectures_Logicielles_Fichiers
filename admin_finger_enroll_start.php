<?php
/**
 * admin_finger_enroll_start.php (Corrigé)
 */
header("Content-Type: application/json; charset=utf-8");
require "auth_middleware.php";
require_admin();
require "config_db.php";

$conn = get_db();

$user_id  = isset($_POST['user_id'])  ? (int)$_POST['user_id']  : 0;
$salle_id = isset($_POST['salle_id']) ? (int)$_POST['salle_id'] : 0;

if ($user_id <= 0 || $salle_id <= 0) {
    echo json_encode(["success" => false, "message" => "Paramètres invalides"]);
    exit;
}

// ── 1. Vérifier que l'utilisateur existe ────────────────────────────────────────
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion DB"]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Utilisateur introuvable"]);
    exit;
}
$stmt->close();

// ── 2. Annuler les anciens enrôlements en attente pour cette salle ────────────
$stmt = $conn->prepare("UPDATE finger_enrollments SET status = 'cancelled' WHERE salle_id = ? AND status = 'waiting'");
if ($stmt) {
    $stmt->bind_param("i", $salle_id);
    $stmt->execute();
    $stmt->close();
}

// ── 3. Créer la nouvelle demande d'enrôlement ─────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO finger_enrollments (user_id, salle_id, status, date_creation)
     VALUES (?, ?, 'waiting', NOW())"
);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Erreur : La table 'finger_enrollments' existe-t-elle ?"]);
    exit;
}
$stmt->bind_param("ii", $user_id, $salle_id);
if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Erreur d'insertion en base"]);
    exit;
}
$stmt->close();

// ── 4. Passer la serrure en mode finger_enroll directement en base ────────────
$stmt = $conn->prepare("UPDATE serrures SET mode = 'finger_enroll' WHERE salle_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $salle_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode([
    "success" => true,
    "message" => "Enrôlement démarré — posez le doigt sur le capteur"
]);
?>