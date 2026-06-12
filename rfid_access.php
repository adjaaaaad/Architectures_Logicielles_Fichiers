<?php
/**
 * rfid_access.php
 * Contrôle d'accès RFID : appelé par l'ESP32 à chaque passage de badge.
 *
 * Usage : GET rfid_access.php?uid=AABBCCDD&numero=101
 *
 * Logique :
 *   1. Valider UID et numéro de salle
 *   2. Vérifier que le badge est connu
 *   3. Vérifier que l'utilisateur a accès à cette salle
 *   4. Vérifier que la serrure est en mode 'access' (pas 'enroll')
 *   5. Toggler l'état (ouvert ↔ fermé)
 *   6. Logger l'action
 */
header("Content-Type: application/json; charset=utf-8");
require "config_db.php";

$conn = get_db();

// ── Récupération et validation des paramètres ────────────────────────────────
$uid    = isset($_GET['uid'])    ? strtoupper(trim($_GET['uid']))    : '';
$numero = isset($_GET['numero']) ? trim($_GET['numero'])             : '';

if (empty($uid) || empty($numero)) {
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit;
}

// Valider le format UID (hex uniquement, 8–28 caractères)
if (!preg_match('/^[0-9A-F]{8,28}$/', $uid)) {
    echo json_encode(["success" => false, "message" => "Format UID invalide"]);
    exit;
}

// ── 1. Vérifier que le badge est connu ──────────────────────────────────────
// BUG CORRIGÉ : "WHERE rfid_uid = '$uid'" → injection SQL
$stmt = $conn->prepare("SELECT id FROM users WHERE rfid_uid = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("s", $uid);
$stmt->execute();
$resUser = $stmt->get_result();

if ($resUser->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Badge inconnu"]);
    exit;
}
$user_id = (int)$resUser->fetch_assoc()['id'];
$stmt->close();

// ── 2. Vérifier que la salle existe ─────────────────────────────────────────
// BUG CORRIGÉ : "WHERE numero_salle = '$numero'" → injection SQL
$stmt = $conn->prepare("SELECT id FROM salles WHERE numero_salle = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("s", $numero);
$stmt->execute();
$resSalle = $stmt->get_result();

if ($resSalle->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Salle inexistante"]);
    exit;
}
$salle_id = (int)$resSalle->fetch_assoc()['id'];
$stmt->close();

// ── 3. Vérifier l'accès de l'utilisateur ────────────────────────────────────
// BUG CORRIGÉ : variables directement interpolées dans la requête
$stmt = $conn->prepare("SELECT id FROM access WHERE user_id = ? AND salle_id = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("ii", $user_id, $salle_id);
$stmt->execute();
$resAccess = $stmt->get_result();

if ($resAccess->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit;
}
$stmt->close();

// ── 4. Récupérer l'état et le mode de la serrure ────────────────────────────
$stmt = $conn->prepare("SELECT etat, mode FROM serrures WHERE salle_id = ? LIMIT 1");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $salle_id);
$stmt->execute();
$resSerrure = $stmt->get_result();

if ($resSerrure->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Serrure introuvable"]);
    exit;
}
$serrure = $resSerrure->fetch_assoc();
$stmt->close();

// Bloquer l'accès si la serrure est en mode enrôlement
if ($serrure['mode'] === 'enroll') {
    echo json_encode(["success" => false, "message" => "Serrure en mode enrôlement"]);
    exit;
}

// ── 5. Toggler l'état (ouvert ↔ fermé) ──────────────────────────────────────
$newEtat = ($serrure['etat'] == 1) ? 0 : 1;

$stmt = $conn->prepare("UPDATE serrures SET etat = ? WHERE salle_id = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("ii", $newEtat, $salle_id);

if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Erreur de mise à jour"]);
    exit;
}
$stmt->close();

// ── 6. Logger l'action ───────────────────────────────────────────────────────
$action = ($newEtat === 1) ? "ouverte (RFID)" : "fermee (RFID)";
$stmt = $conn->prepare("INSERT INTO logs (user_id, salle_id, action) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("iis", $user_id, $salle_id, $action);
    $stmt->execute();
    $stmt->close();
}

// ── Réponse ──────────────────────────────────────────────────────────────────
echo json_encode([
    "success" => true,
    "etat"    => $newEtat,
    "message" => $newEtat === 1 ? "Porte ouverte" : "Porte fermée"
]);

$conn->close();

// ── Helpers ──────────────────────────────────────────────────────────────────
function _dbError(mysqli $conn): never {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur interne du serveur"]);
    $conn->close();
    exit;
}
?>
