<?php
/**
 * dashboard.php
 * Routeur — redirige selon le rôle de l'utilisateur connecté.
 */
require "auth_middleware.php";
require_auth();

if ($_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
} else {
    header('Location: user_dashboard.php');
}
exit;
