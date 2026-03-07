<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CRUD - PHP 8.x + MySQL</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { margin-bottom: 2rem; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">CRUD MVP</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="produtos_listar.php">Produtos</a>
                </li>
                <?php if ($_SESSION['user_perfil'] === 'ADMIN'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios_listar.php">Usuários</a>
                </li>
                <?php
    endif; ?>
            </ul>
            <span class="navbar-text me-3 text-light">
                Olá, <strong><?php echo htmlspecialchars($_SESSION['user_nome']); ?></strong> 
                <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($_SESSION['user_perfil']); ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>
<?php
endif; ?>

<main class="container">
    <!-- Exibição de Mensagens Globais de Sucesso ou Erro -->
    <?php
if (isset($_SESSION['sucesso'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['sucesso']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['sucesso']);
}
if (isset($_SESSION['erro'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['erro']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['erro']);
}
?>
