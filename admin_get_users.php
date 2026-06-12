<?php
/**
 * admin_get_users.php
 * Retourne la liste de tous les utilisateurs. Admin seulement.
 */

require "auth_middleware.php";
require "config_db.php";
api_require_admin();

header('Content-Type: application/json');

$conn = get_db();

$res = $conn->query(
    "SELECT u.id, u.nom, u.role, COUNT(DISTINCT a.salle_id) AS nb_salles
     FROM users u
     LEFT JOIN access a ON a.user_id = u.id
     GROUP BY u.id, u.nom, u.role
     ORDER BY u.role DESC, u.nom ASC"
);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();
echo json_encode($data);
