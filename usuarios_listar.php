<?php
require_once 'includes/auth.php';
// Restrito a ADMIN
checkAdmin();

require_once 'config/database.php';

// Busca básica pelo nome ou email do usuário
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

$query = "SELECT id, nome, email, perfil, data_criacao FROM usuarios";
$params = [];

if ($busca !== '') {
    $query .= " WHERE nome LIKE :busca OR email LIKE :busca";
    $params['busca'] = "%$busca%";
}

$query .= " ORDER BY data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

$csrfToken = generateCSRFToken();

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciamento de Usuários</h2>
    <a href="usuarios_criar.php" class="btn btn-success shadow-sm">
        + Novo Usuário
    </a>
</div>

<!-- Filtro de Busca -->
<div class="card shadow-sm mb-4 border-0 bg-white">
    <div class="card-body">
        <form method="GET" action="usuarios_listar.php" class="row gx-2 gy-2 align-items-center">
            <div class="col-md-10">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou e-mail..." value="<?php echo htmlspecialchars($busca); ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary text-white text-uppercase fw-bold">Buscar</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Usuários -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Conta Criada Em</th>
                        <th class="text-center pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($usuarios) > 0): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-muted"><?php echo (int)$u['id']; ?></td>
                                <td class="fw-semibold text-primary"><?php echo htmlspecialchars($u['nome']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <?php if ($u['perfil'] === 'ADMIN'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm text-uppercase">Admin</span>
                                    <?php
        else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm text-uppercase">User</span>
                                    <?php
        endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($u['data_criacao'])); ?></td>
                                <td class="text-center pe-3">
                                    <a href="usuarios_editar.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning text-dark fw-bold me-1">Editar</a>
                                    
                                    <!-- Impede o admin de se excluir acidentalmente -->
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                        <form action="usuarios_deletar.php" method="POST" class="d-inline" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este usuário?');">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold">Excluir</button>
                                        </form>
                                    <?php
        else: ?>
                                        <button class="btn btn-sm btn-secondary fw-bold disabled" aria-disabled="true">Excluir</button>
                                    <?php
        endif; ?>
                                </td>
                            </tr>
                        <?php
    endforeach; ?>
                    <?php
else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
