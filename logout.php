<?php
// logout.php
session_start();

// Esvazia e destrói os dados da sessão
session_unset();
session_destroy();

// Redireciona de volta para garantir uma limpeza total no lado cliente (cookies)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

header("Location: login.php");
exit;
