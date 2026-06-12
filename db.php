<?php
/**
 * config/db.php
 * Connexion centralisée à la base de données.
 * Utiliser get_db() pour obtenir une instance mysqli.
 */

define('DB_HOST',   'mysql-kinesis.alwaysdata.net');
define('DB_NAME',   'kinesis_bd');
define('DB_USER',   'kinesis');
define('DB_PASS',   'gpr-32026');
define('DB_CHARSET','utf8mb4');

function get_db(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Erreur base de données']));
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
