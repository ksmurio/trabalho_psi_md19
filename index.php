<?php
require_once 'includes/config.php';
$page_title = 'Início - Biblioteca Online';


$sql = "SELECT * FROM livros ORDER BY data_adicao DESC LIMIT 6";
$result = $conn->query($sql);
$livros_destaque = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $livros_destaque[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="jumbotron bg-light p-5 rounded-3 mb-4">
        <div class="container-fluid py-5">
            <h1 class="display-4 fw-bold">Bem-vindo à Biblioteca Online</h1>
            <p class="col-md-8 fs-5">Explore o nosso catálogo, requisite livros e gerencie os seus empréstimos de forma simples e prática.</p>
            <a href="livros.php" class="btn btn-primary btn-lg">
                <i class="bi bi-book"></i> Ver Catálogo
            </a>
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <a href="registar.php" class="btn btn-success btn-lg">
                    <i class="bi bi-person-plus"></i> Criar Conta
                </a>
            <?php endif; ?>
        </div>
    </div>
  
    <div class="row text-center mb-5">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <i class="bi bi-book-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM livros");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Livros no Catálogo</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <i class="bi bi-people-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo='aluno'");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Utilizadores Registados</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <i class="bi bi-bookmark-check-fill text-warning" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">
                        <?php
                        $result = $conn->query("SELECT COUNT(*) as total FROM emprestimos WHERE status='ativo'");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </h3>
                    <p class="text-muted">Empréstimos Ativos</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mb-4"><i class="bi bi-star-fill text-warning"></i> Livros em Destaque</h2>
    <div class="row">
        <?php if (count($livros_destaque) > 0): ?>
            <?php foreach ($livros_destaque as $livro): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="bi bi-book text-white" style="font-size: 5rem;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($livro['autor']); ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo $livro['ano']; ?>
                                </small>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($livro['disponivel'] > 0): ?>
                                    <span class="badge bg-success">Disponível: <?php echo $livro['disponivel']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Indisponível</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Ainda não há livros disponíveis no catálogo.
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="livros.php" class="btn btn-outline-primary btn-lg">
            Ver Todos os Livros <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
