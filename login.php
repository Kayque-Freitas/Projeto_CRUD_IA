<?php
// login.php
require_once 'config/database.php';
session_start();

// Se já estiver logado, redirecionar para o dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização de Entrada
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        // Prepared Statement para evitar SQL Injection
        $stmt = $pdo->prepare("SELECT id, nome, senha, perfil FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verificação segura da senha usando password_verify
        if ($user && password_verify($senha, $user['senha'])) {
            // Previne Session Fixation Attack regenerando o ID
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_perfil'] = $user['perfil'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "E-mail ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD MVP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>

<div class="card shadow-sm login-card">
    <div class="card-header bg-primary text-white text-center py-3">
        <h4 class="mb-0 fw-bold">Acesso ao Sistema</h4>
    </div>
    <div class="card-body p-4">
        <?php if ($erro): ?>
            <div class="alert alert-danger p-2 text-center"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="admin@admin.com" required autofocus>
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label fw-bold">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" placeholder="******" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
