<?php
require_once 'includes/auth.php';
// Restrito a ADMIN
checkAdmin();

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        // Trava para impedir do Admin excluir sua própria conta se estiver logado
        if ($id == $_SESSION['user_id']) {
            die("Você não pode excluir sua própria conta enquanto estiver logado.");
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
        catch (PDOException $e) {
            die("Erro ao excluir usuário: " . $e->getMessage());
        }
    }
}

header("Location: usuarios_listar.php");
exit;
