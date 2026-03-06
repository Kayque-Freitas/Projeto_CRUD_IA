<?php
require_once 'includes/auth.php';
// Requer login
checkLogin();

require_once 'config/database.php';

// Busca básica
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

$query = "SELECT * FROM produtos";
$params = [];

if ($busca !== '') {
    $query .= " WHERE nome LIKE :busca OR descricao LIKE :busca";
    $params['busca'] = "%$busca%";
}

// Ordem decrescente de criação
$query .= " ORDER BY data_criacao DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

// Facilita verificação de perfil na view
$isAdmin = ($_SESSION['user_perfil'] === 'ADMIN');
$csrfToken = $isAdmin ? generateCSRFToken() : '';

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Catálogo de Produtos</h2>
    <?php if ($isAdmin): ?>
        <a href="produtos_criar.php" class="btn btn-success shadow-sm">
            + Novo Produto
        </a>
    <?php endif; ?>
</div>

<!-- Filtro de Busca -->
<div class="card shadow-sm mb-4 border-0 bg-white">
    <div class="card-body">
        <form method="GET" action="produtos_listar.php" class="row gx-2 gy-2 align-items-center">
            <div class="col-md-10">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou descrição..." value="<?php echo htmlspecialchars($busca); ?>">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary text-white text-uppercase fw-bold">Buscar</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabela -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço (R$)</th>
                        <th>Cadastro</th>
                        <?php if ($isAdmin): ?>
                            <th class="text-center pe-3">Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produtos) > 0): ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-muted"><?php echo (int) $p['id']; ?></td>
                                <td class="fw-semibold text-primary"><?php echo htmlspecialchars($p['nome']); ?></td>
                                <td class="text-truncate" style="max-width: 250px;">
                                    <?php echo htmlspecialchars($p['descricao']); ?>
                                </td>
                                <td><?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($p['data_criacao'])); ?></td>
                                <?php if ($isAdmin): ?>
                                    <td class="text-center pe-3">
                                        <a href="produtos_editar.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-warning text-dark fw-bold me-1">Editar</a>
                                        <form action="produtos_deletar.php" method="POST" class="d-inline" onsubmit="return confirm('ATENÇÃO: Deseja realmente excluir este produto da base de dados? Essa ação não pode ser desfeita.');">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold">Excluir</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo $isAdmin ? '6' : '5'; ?>" class="text-center py-4 text-muted">
                                Nenhum produto encontrado para a busca atual.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
