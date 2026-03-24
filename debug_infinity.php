<?php
// debug_infinity.php - Faça upload deste arquivo para o InfinityFree e acesse no navegador
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico de Login - InfinityFree</h1>";

echo "<h3>1. Teste de Conexão com o Banco</h3>";
try {
    require_once 'config/database.php';
    if (isset($pdo)) {
        echo "<p style='color:green; font-weight:bold;'>[ OK ] Conexão com o banco estabelecida com sucesso!</p>";
    }
    else {
        echo "<p style='color:red; font-weight:bold;'>[ FALHA ] Variável \$pdo não encontrada.</p>";
        exit;
    }
}
catch (Exception $e) {
    echo "<p style='color:red; font-weight:bold;'>[ ERRO CRÍTICO ] Falha ao conectar. Verifique config/database.php</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    exit;
}

echo "<hr>";

echo "<h3>2. Teste da Tabela 'usuarios' (Verificação do Admin)</h3>";
try {
    $stmt = $pdo->query("SELECT id, nome, email, senha FROM usuarios WHERE email = 'admin@admin.com'");
    $user = $stmt->fetch();

    if ($user) {
        echo "<p style='color:green; font-weight:bold;'>[ OK ] Usuário 'admin@admin.com' encontrado!</p>";
        echo "<pre style='background:#f4f4f4; padding:10px;'>";
        echo "ID: " . htmlspecialchars($user['id']) . "\n";
        echo "Nome: " . htmlspecialchars($user['nome']) . "\n";
        echo "E-mail: " . htmlspecialchars($user['email']) . "\n";
        echo "Hash salvo: " . htmlspecialchars($user['senha']) . "\n";
        echo "</pre>";

        echo "<hr>";
        echo "<h3>3. Teste de Validação da Senha 'admin123'</h3>";

        $senha_teste = 'admin123';
        if (password_verify($senha_teste, $user['senha'])) {
            echo "<p style='color:green; font-weight:bold; font-size:18px;'>[ SUCESSO ] O hash e a senha '$senha_teste' COMBINAM! O login DEVE estar funcionando.</p>";
            echo "<p>Se ainda não loga, o problema pode estar nas Sessões do PHP (Session Save Path).</p>";
        }
        else {
            echo "<p style='color:red; font-weight:bold; font-size:18px;'>[ FALHA ] A senha '$senha_teste' NÃO BATE com o hash salvo no banco!</p>";
            echo "<p>Sugestão: Volte ao phpMyAdmin e rode o comando UPDATE novamente com muito cuidado para não copiar espaços em branco extras.</p>";
        }
    }
    else {
        echo "<p style='color:red; font-weight:bold;'>[ ERRO ] O usuário 'admin@admin.com' NÃO foi encontrado na tabela.</p>";
        $stmt_total = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
        $total = $stmt_total->fetch()['total'];
        echo "<p>Total de usuários na tabela: $total. Verifique se você realmente inseriu os dados com o email correto.</p>";
    }
}
catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold;'>[ ERRO CRÍTICO ] Falha ao ler tabela usuarios: " . $e->getMessage() . "</p>";
    echo "<p>Provavelmente a tabela NÃO FOI CRIADA no InfinityFree. Verifique no phpMyAdmin.</p>";
}
echo "<hr>";
echo "<p><i>Lembre-se de excluir este arquivo após resolver o problema por segurança.</i></p>";
?>
