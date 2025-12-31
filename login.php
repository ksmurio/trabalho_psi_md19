<?php
require_once 'includes/config.php';
$page_title = 'Login - Biblioteca Online';

// Redirecionar se já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        $stmt = $conn->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();
            
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo'] = $usuario['tipo'];
                
                // Redirecionar
                if ($usuario['tipo'] == 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $erro = 'Email ou senha incorretos.';
            }
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </h2>
                    
                    <?php if ($erro): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">
                                <i class="bi bi-lock"></i> Senha
                            </label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Entrar
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="alert alert-info">
                        <strong>Utilizador de Teste:</strong><br>
                        Email: admin@biblioteca.pt<br>
                        Senha: admin123
                    </div>
                    
                    <p class="text-center mb-0">
                        Não tem conta? <a href="registar.php">Criar Conta</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
