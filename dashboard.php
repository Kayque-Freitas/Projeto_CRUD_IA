<?php
// dashboard.php
require_once 'includes/auth.php';
// Verifica Sessão Protegida
checkLogin();

require_once 'config/database.php';

// Coletando métricas básicas
$stmtProd = $pdo->query("SELECT COUNT(*) as total FROM produtos");
$totalProdutos = $stmtProd->fetch()['total'];

$stmtUsu = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$totalUsuarios = $stmtUsu->fetch()['total'];

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <!-- Card Info Produtos -->
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-primary h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-uppercase opacity-75">Total de Produtos</h5>
                <h2 class="display-4 fw-bold mb-0 text-white"><?php echo $totalProdutos; ?></h2>
            </div>
            <div class="card-footer bg-transparent border-0 opacity-75">
                Cadastrados no sistema
            </div>
        </div>
    </div>
    
    <!-- Card Info Usuários -->
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-success h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-uppercase opacity-75">Total de Usuários</h5>
                <h2 class="display-4 fw-bold mb-0 text-white"><?php echo $totalUsuarios; ?></h2>
            </div>
            <div class="card-footer bg-transparent border-0 opacity-75">
                Com acesso à plataforma
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-2 border-0">
    <div class="card-body">
        <h4 class="card-title text-primary">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_nome']); ?>!</h4>
        <p class="card-text">
            Este é o painel administrativo do <strong>Sistema CRUD</strong>. 
            Você está acessando com o perfil de <strong><?php echo htmlspecialchars($_SESSION['user_perfil']); ?></strong>.
        </p>
        <p class="text-muted">
            Utilize o menu superior para navegar nas funções que você tem acesso.
        </p>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>
