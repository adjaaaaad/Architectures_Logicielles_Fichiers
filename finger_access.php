<?php
/**
 * finger_access.php
 * Contrôle d'accès par empreinte digitale : appelé par l'ESP32 à chaque lecture.
 *
 * Usage : GET finger_access.php?fingerprint_id=3&numero=101
 *
 * Logique :
 *   1. Valider fingerprint_id et numéro de salle
 *   2. Vérifier que l'empreinte est connue et récupérer l'utilisateur associé
 *   3. Vérifier que l'utilisateur a accès à cette salle
 *   4. Vérifier que la serrure est en mode 'access' (pas 'finger_enroll')
 *   5. Toggler l'état (ouvert ↔ fermé)
 *   6. Logger l'action
 *
 * Réponse JSON identique à rfid_access.php pour cohérence ESP32.
 */
header("Content-Type: application/json; charset=utf-8");
require "config_db.php";

$conn = get_db();

// ── Récupération et validation des paramètres ────────────────────────────────
$fingerprint_id = isset($_GET['fingerprint_id']) ? trim($_GET['fingerprint_id']) : '';
$numero         = isset($_GET['numero'])         ? trim($_GET['numero'])         : '';

if ($fingerprint_id === '' || $numero === '') {
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit;
}

// Valider que fingerprint_id est un entier positif (1–127, limite capteur AS608)
if (!ctype_digit($fingerprint_id) || (int)$fingerprint_id < 1 || (int)$fingerprint_id > 127) {
    echo json_encode(["success" => false, "message" => "fingerprint_id invalide"]);
    exit;
}
$fingerprint_id = (int)$fingerprint_id;

// ── 1. Vérifier que l'empreinte est connue et récupérer l'utilisateur ────────
$stmt = $conn->prepare(
    "SELECT e.utilisateur_id, u.id AS user_id
     FROM empreintes e
     JOIN users u ON u.id = e.utilisateur_id
     WHERE e.fingerprint_id = ?
     LIMIT 1"
);
if (!$stmt) { _dbError($conn); }
$stmt->bind_param("i", $fingerprint_id);
$stmt->execute();
$resEmp = $stmt->get_result();

if ($resEmp->num_rows === 0) {
    $stmt->close();
    echo json_encode(["success" => false, "message" => "Empreinte inconnue"]);
    exit;
}
$user_id = (int)$resEmp->fetch_assoc()['user_id'];
$stmt->close();

// ── 2. Vérifier que la salle existe ──────────────────────────────────────────
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

// ── 3. Vérifier l'accès de l'utilisateur ─────────────────────────────────────
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

// ── 4. Récupérer l'état et le mode de la serrure ─────────────────────────────
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

// Bloquer l'accès si la serrure est en mode enrôlement empreinte
if ($serrure['mode'] === 'finger_enroll') {
    echo json_encode(["success" => false, "message" => "Serrure en mode enrôlement empreinte"]);
    exit;
}

// Bloquer l'accès si la serrure est en mode enrôlement RFID (cohérence)
if ($serrure['mode'] === 'enroll') {
    echo json_encode(["success" => false, "message" => "Serrure en mode enrôlement RFID"]);
    exit;
}

// ── 5. Toggler l'état (ouvert ↔ fermé) ───────────────────────────────────────
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
$action = ($newEtat === 1) ? "ouverte (Empreinte)" : "fermee (Empreinte)";
$stmt = $conn->prepare("INSERT INTO logs (user_id, salle_id, action) VALUES (?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("iis", $user_id, $salle_id, $action);
    $stmt->execute();
    $stmt->close();
}

// ── Réponse ───────────────────────────────────────────────────────────────────
echo json_encode([
    "success" => true,
    "etat"    => $newEtat,
    "message" => $newEtat === 1 ? "Porte ouverte" : "Porte fermée"
]);

$conn->close();

// ── Helpers ───────────────────────────────────────────────────────────────────
function _dbError(mysqli $conn): never {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur interne du serveur"]);
    $conn->close();
    exit;
}
?>
