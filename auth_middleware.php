<?php
/**
 * auth_middleware.php
 * Fonctions de sécurité — à inclure en tête de chaque page protégée.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Redirige vers login si pas connecté. */
function require_auth(): void {
    if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

/** Redirige vers user_dashboard si pas admin. */
function require_admin(): void {
    require_auth();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: user_dashboard.php');
        exit;
    }
}

/** Pour les APIs : retourne JSON 401 si pas connecté. */
function api_require_auth(): void {
    if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        exit;
    }
}

/** Pour les APIs : retourne JSON 403 si pas admin. */
function api_require_admin(): void {
    api_require_auth();
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Accès refusé']);
        exit;
    }
}
