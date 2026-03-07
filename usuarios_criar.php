<?php
require_once 'includes/auth.php';
// Restrito a ADMIN
checkAdmin();

require_once 'config/database.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'USER';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Todos os campos (nome, e-mail, senha) são obrigatórios.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    }
    elseif (!in_array($perfil, ['ADMIN', 'USER'])) {
        $erro = "Perfil inválido selecionado.";
    }
    else {
        // Verifica se o e-mail já existe
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt_check->execute(['email' => $email]);

        if ($stmt_check->rowCount() > 0) {
            $erro = "Este e-mail já está sendo utilizado por outro usuário.";
        }
        else {
            // Insere o usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)");
                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha_hash,
                    'perfil' => $perfil
                ]);
                $sucesso = "Usuário '$nome' criado com sucesso!";
            }
            catch (PDOException $e) {
                $erro = "Erro ao criar usuário: " . $e->getMessage();
            }
        }
    }
}

$csrfToken = generateCSRFToken();
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Criar Novo Usuário</h2>
    <a href="usuarios_listar.php" class="btn btn-secondary shadow-sm">
        &larr; Voltar
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php if ($erro): ?>
            <div class="alert alert-danger shadow-sm border-0"><?php echo htmlspecialchars($erro); ?></div>
        <?php
endif; ?>
        <?php if ($sucesso): ?>
            <div class="alert alert-success shadow-sm border-0"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php
endif; ?>

        <form method="POST" action="usuarios_criar.php">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label fw-bold">Nome Completo</label>
                    <input type="text" name="nome" id="nome" class="form-control" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" required maxlength="100">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="senha" class="form-label fw-bold">Senha Inicial</label>
                    <input type="password" name="senha" id="senha" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>
                <div class="col-md-6">
                    <label for="perfil" class="form-label fw-bold">Perfil de Acesso</label>
                    <select name="perfil" id="perfil" class="form-select" required>
                        <option value="USER" selected>USER (Apenas visualização)</option>
                        <option value="ADMIN">ADMIN (Acesso Total)</option>
                    </select>
                </div>
            </div>

            <hr>
            
            <button type="submit" class="btn btn-success fw-bold px-4">Salvar Usuário</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
