<?php
// reset_senha.php
require_once 'config/database.php';

$nova_senha = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = 'admin@admin.com'");
$stmt->execute(['senha' => $nova_senha]);

echo "<h1>Senha resetada com sucesso!</h1>";
echo "<p>O novo hash salvo no banco para o admin@admin.com é: <br><b>$nova_senha</b></p>";
echo "<p><a href='login.php'>Clique aqui para fazer o login</a> usando 'admin123'. Lembre-se de excluir este arquivo após o uso.</p>";
?>
