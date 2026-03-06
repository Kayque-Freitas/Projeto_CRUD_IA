<?php
require_once 'includes/auth.php';
// Restringe a página a usuários com perfil ADMIN. Irá redirecionar e exibir erro se for USER.
checkAdmin();
require_once 'config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação de segurança primária
    verifyCSRFToken($_POST['csrf_token'] ?? '');

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    // Regex e substituições para lidar com valor monetário inserido numérico ou com vírgula BR
    $preco_raw = $_POST['preco'] ?? '';
    $preco = str_replace(',', '.', str_replace('.', '', $preco_raw)); // remove pontos (milhar) e troca vírgula por ponto

    if (!empty($nome) && is_numeric($preco) && $preco > 0) {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco) VALUES (:nome, :descricao, :preco)");
        
        try {
            if ($stmt->execute(['nome' => $nome, 'descricao' => $descricao, 'preco' => $preco])) {
                $_SESSION['sucesso'] = "Produto cadastrado com sucesso e já listado.";
                header("Location: produtos_listar.php");
                exit;
            } else {
                $erro = "Erro interno no banco ao cadastrar o produto.";
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    } else {
        $erro = "Verifique o preenchimento. O Nome é obrigatório e o Preço deve ser um valor válido (ex: 1500,50).";
    }
}

$csrfToken = generateCSRFToken();
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white py-3">
                <h4 class="mb-0 fw-bold">Cadastrar Novo Produto</h4>
            </div>
            <div class="card-body p-4 border bg-white">
                <?php if ($erro): ?>
                    <div class="alert alert-danger p-2"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="produtos_criar.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-bold text-muted">Nome do Produto <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome" class="form-control border-secondary-subtle" required maxlength="150" placeholder="Ex: Notebook Avell A70" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-bold text-muted">Descrição Simples</label>
                        <textarea name="descricao" id="descricao" class="form-control border-secondary-subtle" rows="4" placeholder="Detalhes, especificações e diferenciais do produto."><?php echo isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="preco" class="form-label fw-bold text-muted">Preço (R$) <span class="text-danger">*</span></label>
                        <input type="text" name="preco" id="preco" class="form-control border-secondary-subtle" required placeholder="0,00" value="<?php echo isset($_POST['preco']) ? htmlspecialchars($_POST['preco']) : ''; ?>">
                        <div class="form-text">Pode inserir casas decimais separadas por vírgula (ex: 150,50).</div>
                    </div>
                    
                    <div class="d-flex justify-content-between pt-2">
                        <a href="produtos_listar.php" class="btn btn-outline-secondary">Voltar para Lista</a>
                        <button type="submit" class="btn btn-success fw-bold px-4">Salvar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
