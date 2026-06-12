<?php
/**
 * finger_enroll.php
 * Enregistrement d'une empreinte après enrôlement physique par l'ESP32.
 *
 * Usage : GET finger_enroll.php?fingerprint_id=5&numero=101
 */
header("Content-Type: application/json; charset=utf-8");
require "config_db.php";

$conn = get_db();

// ── Paramètres ────────────────────────────────────────────────────────────────
$fingerprint_id = isset($_GET['fingerprint_id']) ? trim($_GET['fingerprint_id']) : '';
$numero         = isset($_GET['numero'])         ? trim($_GET['numero'])         : '';

if ($fingerprint_id === '' || $numero === '') {
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit;
}

if (!ctype_digit($fingerprint_id) || (int)$fingerprint_id < 1 || (int)$fingerprint_id > 127) {
    echo json_encode(["success" => false, "message" => "fingerprint_id invalide"]);
    exit;
}
$fingerprint_id = (int)$fingerprint_id;

// ── 1. Récupérer la salle ─────────────────────────────────────────────────────
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

// ── 2. Vérifier qu'un enrôlement est en attente pour cette salle ─────────────
$stmt = $conn->prepare(
    "SELECT fe.id, fe.user_id
     FROM finger_enrollments fe
     WHERE fe.status = 'waiting'
       AND fe.salle_id = ?
     ORDER BY fe.id DESC
     LIMIT 1"
);
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $salle_id);
$stmt->execute();
$resEnroll = $stmt->get_result();

if ($resEnroll->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Aucun enrôlement en attente pour cette salle"]);
    exit;
}
$enroll   = $resEnroll->fetch_assoc();
$user_id   = (int)$enroll['user_id'];
// Plus besoin de garder un seul enroll_id, on va tout nettoyer à l'étape 6.
$stmt->close();

// ── 3. Vérifier que l'empreinte n'est pas déjà enregistrée ──────────────────
$stmt = $conn->prepare("SELECT id FROM empreintes WHERE fingerprint_id = ?");
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $fingerprint_id);
$stmt->execute();
$resDup = $stmt->get_result();

if ($resDup->num_rows > 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Cette empreinte est déjà enregistrée"]);
    exit;
}
$stmt->close();

// ── 4. Associer fingerprint_id → utilisateur_id ───────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO empreintes (fingerprint_id, utilisateur_id, date_creation)
     VALUES (?, ?, NOW())"
);
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("ii", $fingerprint_id, $user_id);

if (!$stmt->execute()) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Erreur d'enregistrement de l'empreinte"]);
    exit;
}
$stmt->close();

// ── 5. Repasser la serrure en mode 'access' ───────────────────────────────────
$stmtMode = $conn->prepare(
    "UPDATE serrures
     JOIN salles ON salles.id = serrures.salle_id
     SET serrures.mode = 'access'
     WHERE salles.numero_salle = ?"
);
if ($stmtMode) {
    $stmtMode->bind_param("s", $numero);
    $stmtMode->execute();
    $stmtMode->close();
}

// ── 6. Marquer TOUS les enrôlements de cette salle comme terminés ───────────
// C'est ici la correction majeure : on passe TOUT ce qui est 'waiting' à 'done'
$stmtClean = $conn->prepare("UPDATE finger_enrollments SET status = 'done' WHERE salle_id = ? AND status = 'waiting'");
if ($stmtClean) {
    $stmtClean->bind_param("i", $salle_id);
    $stmtClean->execute();
    $stmtClean->close();
}

// ── Réponse ───────────────────────────────────────────────────────────────────
echo json_encode([
    "success"        => true,
    "message"        => "Empreinte enregistrée avec succès",
    "fingerprint_id" => $fingerprint_id,
    "user_id"        => $user_id
]);

$conn->close();

// ── Helpers ───────────────────────────────────────────────────────────────────
function _dbError(mysqli $conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur interne du serveur"]);
    $conn->close();
    exit;
}
?>