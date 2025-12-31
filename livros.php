<?php
require_once 'includes/config.php';
$page_title = 'Catálogo de Livros - Biblioteca Online';

// Buscar todos os livros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM livros WHERE titulo LIKE ? OR autor LIKE ? ORDER BY titulo ASC";
$stmt = $conn->prepare($sql);
$search_param = "%{$search}%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="bi bi-book-half"></i> Catálogo de Livros</h1>
            <p class="lead">Explore a nossa coleção de livros disponíveis</p>
        </div>
        <div class="col-md-4">
            <form method="GET" class="mt-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Pesquisar livro ou autor..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($search): ?>
        <div class="alert alert-info">
            <i class="bi bi-search"></i> Resultados para: <strong><?php echo htmlspecialchars($search); ?></strong>
            <a href="livros.php" class="btn btn-sm btn-outline-secondary float-end">Limpar</a>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($livro = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                             style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-book text-white" style="font-size: 4rem;"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livro['titulo']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($livro['autor']); ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> Ano: <?php echo $livro['ano']; ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-hash"></i> ISBN: <?php echo htmlspecialchars($livro['isbn']); ?>
                                </small>
                            </p>
                            <?php if ($livro['descricao']): ?>
                                <p class="card-text small"><?php echo htmlspecialchars(substr($livro['descricao'], 0, 100)) . '...'; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($livro['disponivel'] > 0): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Disponível: <?php echo $livro['disponivel']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Indisponível
                                    </span>
                                <?php endif; ?>
                                <small class="text-muted">Total: <?php echo $livro['quantidade']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Nenhum livro encontrado.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
