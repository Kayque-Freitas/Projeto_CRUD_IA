<?php
require_once 'includes/auth.php';
// Restrito a ADMIN
checkAdmin();

require_once 'config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';
$sucesso = '';

if (!$id) {
    header("Location: usuarios_listar.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $perfil = $_POST['perfil'] ?? 'USER';
    $nova_senha = $_POST['senha'] ?? '';

    // Proibir Admin de tirar o próprio poder
    if ($id == $_SESSION['user_id'] && $perfil !== 'ADMIN') {
        $erro = "Você não pode remover o próprio acesso administrativo.";
    }
    elseif (empty($nome) || empty($email)) {
        $erro = "Nome e e-mail são obrigatórios.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    }
    elseif (!in_array($perfil, ['ADMIN', 'USER'])) {
        $erro = "Perfil inválido selecionado.";
    }
    else {
        // Verifica se o email já existe para OUTRO usuário
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
        $stmt_check->execute(['email' => $email, 'id' => $id]);

        if ($stmt_check->rowCount() > 0) {
            $erro = "Este e-mail já está sendo utilizado por outro usuário.";
        }
        else {
            try {
                // Se preencheu a senha, atualiza a senha também.
                if (!empty($nova_senha)) {
                    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, senha = :senha WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'perfil' => $perfil,
                        'senha' => $senha_hash,
                        'id' => $id
                    ]);
                }
                else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id");
                    $stmt->execute([
                        'nome' => $nome,
                        'email' => $email,
                        'perfil' => $perfil,
                        'id' => $id
                    ]);
                }

                // Se atualizou ele mesmo, propaga pra sessão ficar correta
                if ($id == $_SESSION['user_id']) {
                    $_SESSION['user_nome'] = $nome;
                }

                $sucesso = "Usuário atualizado com sucesso!";
            }
            catch (PDOException $e) {
                $erro = "Erro ao editar usuário: " . $e->getMessage();
            }
        }
    }
}

// Buscar dados atuais após possível POST
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios_listar.php");
    exit;
}

$csrfToken = generateCSRFToken();
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Usuário: <?php echo htmlspecialchars($usuario['nome']); ?></h2>
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

        <form method="POST" action="usuarios_editar.php?id=<?php echo $id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label fw-bold">Nome Completo</label>
                    <input type="text" name="nome" id="nome" class="form-control" required value="<?php echo htmlspecialchars($usuario['nome']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">E-mail</label>
                    <input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($usuario['email']); ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="senha" class="form-label fw-bold">Nova Senha (Opcional)</label>
                    <input type="password" name="senha" id="senha" class="form-control" minlength="6" placeholder="Deixe em branco para manter a atual">
                </div>
                <div class="col-md-6">
                    <label for="perfil" class="form-label fw-bold">Perfil de Acesso</label>
                    <select name="perfil" id="perfil" class="form-select" required <?php echo($id == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                        <option value="USER" <?php if ($usuario['perfil'] === 'USER')
    echo 'selected'; ?>>USER</option>
                        <option value="ADMIN" <?php if ($usuario['perfil'] === 'ADMIN')
    echo 'selected'; ?>>ADMIN</option>
                    </select>
                    <?php if ($id == $_SESSION['user_id']): ?>
                        <small class="text-muted d-block mt-1">Você não pode alterar o seu próprio perfil.</small>
                        <!-- Campo hidden pq disabled não é enviado no POST -->
                        <input type="hidden" name="perfil" value="ADMIN">
                    <?php
endif; ?>
                </div>
            </div>

            <hr>
            
            <button type="submit" class="btn btn-warning fw-bold px-4">Salvar Alterações</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
