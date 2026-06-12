<?php
/**
 * cancel_enroll.php
 * Annule un enrôlement d'empreinte en attente.
 * Paramètres acceptés :
 *   - numero_salle (recommandé)
 *   - salle_id (compatibilité JS existant)
 */
header("Content-Type: application/json; charset=utf-8");
require "auth_middleware.php";
require_admin();
require "config_db.php";

$conn = get_db();

$numero_salle = isset($_POST['numero_salle']) ? trim($_POST['numero_salle']) : '';
$salle_id = isset($_POST['salle_id']) ? (int)$_POST['salle_id'] : 0;

if ($numero_salle === '' && $salle_id <= 0) {
    echo json_encode(["success" => false, "message" => "Paramètre numero_salle ou salle_id requis"]);
    $conn->close();
    exit;
}

if ($numero_salle !== '') {
    $stmt = $conn->prepare("SELECT id FROM salles WHERE numero_salle = ? LIMIT 1");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Erreur de préparation SQL"]);
        $conn->close();
        exit;
    }
    $stmt->bind_param("s", $numero_salle);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $stmt->close();
        echo json_encode(["success" => false, "message" => "Salle introuvable"]);
        $conn->close();
        exit;
    }
    $salle_id = (int)$result->fetch_assoc()['id'];
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT numero_salle FROM salles WHERE id = ? LIMIT 1");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Erreur de préparation SQL"]);
        $conn->close();
        exit;
    }
    $stmt->bind_param("i", $salle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $stmt->close();
        echo json_encode(["success" => false, "message" => "Salle introuvable"]);
        $conn->close();
        exit;
    }
    $numero_salle = $result->fetch_assoc()['numero_salle'];
    $stmt->close();
}

$stmtMode = $conn->prepare(
    "UPDATE serrures
     JOIN salles ON salles.id = serrures.salle_id
     SET serrures.mode = 'access'
     WHERE salles.numero_salle = ?"
);
if (!$stmtMode) {
    echo json_encode(["success" => false, "message" => "Erreur de préparation SQL"]);
    $conn->close();
    exit;
}
$stmtMode->bind_param("s", $numero_salle);
$stmtMode->execute();
$modeUpdated = $stmtMode->affected_rows;
$stmtMode->close();

$stmtEnroll = $conn->prepare(
    "UPDATE finger_enrollments
     SET status = 'cancelled'
     WHERE salle_id = ?
       AND status = 'waiting'"
);
if (!$stmtEnroll) {
    echo json_encode(["success" => false, "message" => "Erreur de préparation SQL"]);
    $conn->close();
    exit;
}
$stmtEnroll->bind_param("i", $salle_id);
$stmtEnroll->execute();
$enrollmentsUpdated = $stmtEnroll->affected_rows;
$stmtEnroll->close();

$conn->close();

echo json_encode([
    "success" => true,
    "message" => "Enrôlement annulé pour la salle $numero_salle",
    "updated_serrures" => $modeUpdated,
    "updated_enrollments" => $enrollmentsUpdated
]);
