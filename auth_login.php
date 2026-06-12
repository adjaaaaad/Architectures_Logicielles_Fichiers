<?php
/**
 * auth_login.php
 * Traitement du formulaire de connexion.
 * Redirige vers admin_dashboard.php ou user_dashboard.php selon le rôle.
 */

session_start();
require "config_db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$pin = trim($_POST['pin'] ?? '');

if (empty($nom) || empty($pin)) {
    header('Location: login.php?error=champs_vides');
    exit;
}

$conn = get_db();

$stmt = $conn->prepare('SELECT id, nom, role FROM users WHERE nom = ? AND pin = ? LIMIT 1');
$stmt->bind_param('ss', $nom, $pin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: login.php?error=identifiants_incorrects');
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Sécurité : regénérer l'ID de session
session_regenerate_id(true);

$_SESSION['user_id'] = $user['id'];
$_SESSION['nom']     = $user['nom'];
$_SESSION['role']    = $user['role'];

if ($user['role'] === 'admin') {
    header('Location: admin_dashboard.php');
} else {
    header('Location: user_dashboard.php');
}
exit;
