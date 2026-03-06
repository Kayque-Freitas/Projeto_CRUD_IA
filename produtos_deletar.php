<?php
require_once 'includes/auth.php';
// Apenas ADMINs podem realizar exclusão
checkAdmin();
require_once 'config/database.php';

// Somente aceita deleção via POST para impedir deleção acidental via GET injeção (ex: tag img)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Antifraude CSRF Check
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
        
        try {
            if ($stmt->execute(['id' => $id])) {
                $_SESSION['sucesso'] = trim("Produto Removido com sucesso permanentemente do sistema.");
            } else {
                $_SESSION['erro'] = "Incapaz de excluir por restrição não identificada.";
            }
        } catch (PDOException $e) {
            $_SESSION['erro'] = "Erro de integridade relacional ao excluir o produto.";
        }
    } else {
        $_SESSION['erro'] = "Identificador de item malformado.";
    }
} else {
    // Caso seja GET direto por navegador
    $_SESSION['erro'] = "Transação Inválida. Ação bloqueada por segurança.";
}

// Retorna sempre à lista
header("Location: produtos_listar.php");
exit;
