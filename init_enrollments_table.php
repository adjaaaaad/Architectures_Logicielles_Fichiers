<?php
/**
 * init_enrollments_table.php
 * Initialise la table enrollments si elle n'existe pas
 * À exécuter une seule fois (http://localhost/init_enrollments_table.php)
 */
require "config_db.php";

$conn = get_db();

// Vérifier si la table existe
$check = $conn->query("SHOW TABLES LIKE 'enrollments'");

if($check && $check->num_rows === 0){
    // Table n'existe pas, la créer
    $sql = "CREATE TABLE enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        status ENUM('waiting', 'done', 'cancelled') DEFAULT 'waiting',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if($conn->query($sql)){
        echo json_encode(["success"=>true, "message"=>"Table enrollments créée avec succès"]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Erreur: ".$conn->error]);
    }
} else {
    echo json_encode(["success"=>true, "message"=>"Table enrollments existe déjà"]);
}
?>
