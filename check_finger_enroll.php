<?php
/**
 * check_finger_enroll.php
 * Polling du dashboard pour savoir si l'enrôlement empreinte est terminé.
 *
 * Usage : GET check_finger_enroll.php?salle_id=1
 *
 * Réponse :
 *   { "enrolling": true }   → enrôlement toujours en attente
 *   { "enrolling": false }  → terminé (succès ou annulé)
 */
header("Content-Type: application/json; charset=utf-8");
require "auth_middleware.php";
require_admin();
require "config_db.php";

$conn = get_db();

$salle_id = isset($_GET['salle_id']) ? (int)$_GET['salle_id'] : 0;

if ($salle_id <= 0) {
    echo json_encode(["enrolling" => false, "message" => "salle_id invalide"]);
    $conn->close();
    exit;
}

$stmt = $conn->prepare(
    "SELECT status FROM finger_enrollments
     WHERE salle_id = ? AND status = 'waiting'
     ORDER BY id DESC LIMIT 1"
);
if (!$stmt) {
    echo json_encode(["enrolling" => false]);
    $conn->close();
    exit;
}
$stmt->bind_param("i", $salle_id);
$stmt->execute();
$res = $stmt->get_result();

$enrolling = ($res->num_rows > 0);
$stmt->close();
$conn->close();

echo json_encode(["enrolling" => $enrolling]);
?>
