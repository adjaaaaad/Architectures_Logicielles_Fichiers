<?php
/**
 * config_db.php
 * Connexion centralisée à la base de données.
 */



define('DB_HOST',    'mysql-kinesis.alwaysdata.net');
define('DB_NAME',    'kinesis_bd');
define('DB_USER',    'kinesis');
define('DB_PASS',    'gpr-32026');
define('DB_CHARSET', 'utf8mb4');

function get_db(): mysqli {
    date_default_timezone_set('Africa/Dakar');
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Erreur DB : " . $conn->connect_error);
    }
    $conn->set_charset(DB_CHARSET);
    return $conn;
}
