<?php
/**
 * api/admin/get_users.php
 * Retourne la liste de tous les utilisateurs avec leurs accès.
 * Réservé aux admins.
 */

session_start();
require 'middleware/auth.php';
require 'config/db.php';

api_require_admin();

header('Content-Type: application/json');

$conn = get_db();

$sql = "
    SELECT
        u.id,
        u.nom,
        u.role,
        COUNT(DISTINCT a.salle_id) AS nb_salles
    FROM users u
    LEFT JOIN access a ON a.user_id = u.id
    GROUP BY u.id, u.nom, u.role
    ORDER BY u.role DESC, u.nom ASC
";

$result = $conn->query($sql);
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();
echo json_encode($users);
