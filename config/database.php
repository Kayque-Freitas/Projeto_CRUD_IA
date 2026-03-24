<?php
// Configurações Banco de Dados
define('DB_HOST', 'sql113.infinityfree.com');
define('DB_USER', 'if0_41325687');
define('DB_PASS', 'Au1VtoYbja918y3');
define('DB_NAME', 'if0_41325687_crud_php_pdo');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    // Ativa o modo de erros do PDO para lançar Exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Define o modo padrão de fetch para array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de Conexão: " . $e->getMessage());
}
