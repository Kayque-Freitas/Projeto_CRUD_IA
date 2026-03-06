<?php
require_once 'includes/auth.php';
// Restringe a ADMIN
checkAdmin();
require_once 'config/database.php';

// Obtém e valida o ID recebido na URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não houver ID ou se for malformed
if (!$id) {
    $_SESSION['erro'] = "Referência de Produto Inválida.";
    header("Location: produtos_listar.php");
    exit;
}

// Busca as informações atuais do produto
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$produto = $stmt->fetch();

// Se o ID não existir no banco
if (!$produto) {
    $_SESSION['erro'] = "Produto não localizado na base de dados.";
    header("Location: produtos_listar.php");
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Validação
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco_raw = $_POST['preco'] ?? '';
    // Converta formatação pt-br para float MYSQL (pode ter vindo 1.000,50 ou 1000.50)
    $preco = str_replace(',', '.', str_replace('.', '', $preco_raw));

    if (!empty($nome) && is_numeric($preco) && $preco >= 0) {
        $stmtUpdate = $pdo->prepare("UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco WHERE id = :id");
        
        try {
            if ($stmtUpdate->execute(['nome' => $nome, 'descricao' => $descricao, 'preco' => $preco, 'id' => $id])) {
                $_SESSION['sucesso'] = "Produto #{$id} alterado perfeitamente.";
                header("Location: produtos_listar.php");
                exit;
            } else {
                $erro = "Falha crítica na atualização dos dados.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    } else {
        $erro = "Preenchimento inconsistente. Campos * são estritamente obrigatórios e formato de preço numérico.";
    }
}

$csrfToken = generateCSRFToken();
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning py-3 text-dark border-0">
                <h4 class="mb-0 fw-bold">Modificando Produto #<?php echo (int) $produto['id']; ?></h4>
            </div>
            <div class="card-body p-4 border border-warning bg-white">
                <?php if ($erro): ?>
                    <div class="alert alert-danger p-2"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="produtos_editar.php?id=<?php echo $id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-bold text-muted">Nome do Produto <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control border-secondary-subtle" required maxlength="150" value="<?php echo htmlspecialchars($_POST['nome'] ?? $produto['nome']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-bold text-muted">Descrição Detalhada</label>
                        <textarea name="descricao" id="descricao" class="form-control border-secondary-subtle" rows="4"><?php echo htmlspecialchars($_POST['descricao'] ?? $produto['descricao']); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="preco" class="form-label fw-bold text-muted">Preço Un. (R$) <span class="text-danger">*</span></label>
                        <?php 
                            // Exibe usando ponto e vírgula pt-br
                            $valorExibido = isset($_POST['preco']) ? $_POST['preco'] : number_format($produto['preco'], 2, ',', ''); 
                        ?>
                        <input type="text" name="preco" id="preco" class="form-control border-secondary-subtle" required value="<?php echo htmlspecialchars($valorExibido); ?>">
                    </div>
                    
                    <div class="d-flex justify-content-between pt-2">
                        <a href="produtos_listar.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4 shadow-sm">Aplicar Mudanças</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
