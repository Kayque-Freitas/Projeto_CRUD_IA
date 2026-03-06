<?php
// Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado
 */
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Verifica se o usuário logado tem o perfil de ADMIN
 * Se não tiver, redireciona pro dashboard
 */
function checkAdmin() {
    checkLogin();
    if ($_SESSION['user_perfil'] !== 'ADMIN') {
        $_SESSION['erro'] = "Acesso Negado: Área restrita a administradores.";
        header("Location: dashboard.php");
        exit;
    }
}

/**
 * Gera um token CSRF seguro e armazena na sessão
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica a validade do token CSRF recebido via POST
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || hash_equals($_SESSION['csrf_token'], $token) === false) {
        die("Falha de Segurança CSRF. Requisição inválida.");
    }
}
