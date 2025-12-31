<?php
require_once 'includes/config.php';
$page_title = 'Administração - Biblioteca Online';

// Verificar se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'admin') {
    header('Location: index.php');
    exit();
}

$mensagem = '';

// Adicionar novo livro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_livro'])) {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $isbn = trim($_POST['isbn']);
    $ano = intval($_POST['ano']);
    $quantidade = intval($_POST['quantidade']);
    $descricao = trim($_POST['descricao']);
    
    $stmt = $conn->prepare("INSERT INTO livros (titulo, autor, isbn, ano, quantidade, disponivel, descricao) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiss", $titulo, $autor, $isbn, $ano, $quantidade, $quantidade, $descricao);
    
    if ($stmt->execute()) {
        $mensagem = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> Livro adicionado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } else {
        $mensagem = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle"></i> Erro ao adicionar livro.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Editar livro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_livro'])) {
    $id = intval($_POST['id']);
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $isbn = trim($_POST['isbn']);
    $ano = intval($_POST['ano']);
    $quantidade = intval($_POST['quantidade']);
    $descricao = trim($_POST['descricao']);
    
    $stmt = $conn->prepare("UPDATE livros SET titulo=?, autor=?, isbn=?, ano=?, quantidade=?, descricao=? WHERE id=?");
    $stmt->bind_param("sssiisi", $titulo, $autor, $isbn, $ano, $quantidade, $descricao, $id);
    
    if ($stmt->execute()) {
        $mensagem = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> Livro atualizado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Eliminar livro
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conn->prepare("DELETE FROM livros WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $mensagem = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> Livro eliminado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

// Buscar todos os livros
$result_livros = $conn->query("SELECT * FROM livros ORDER BY titulo ASC");

// Buscar empréstimos ativos
$result_emprestimos = $conn->query("SELECT e.*, l.titulo, u.nome, u.email 
                                     FROM emprestimos e 
                                     INNER JOIN livros l ON e.livro_id = l.id 
                                     INNER JOIN usuarios u ON e.usuario_id = u.id 
                                     WHERE e.status = 'ativo' 
                                     ORDER BY e.data_emprestimo DESC");

include 'includes/header.php';
?>

<div class="container">
    <h1 class="mb-4"><i class="bi bi-gear-fill"></i> Painel de Administração</h1>
    
    <?php echo $mensagem; ?>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#livros" type="button">
                <i class="bi bi-book"></i> Gestão de Livros
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#emprestimos" type="button">
                <i class="bi bi-bookmark-check"></i> Empréstimos Ativos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#adicionar" type="button">
                <i class="bi bi-plus-circle"></i> Adicionar Livro
            </button>
        </li>
    </ul>
    
    <div class="tab-content">
        <!-- Tab: Gestão de Livros -->
        <div class="tab-pane fade show active" id="livros">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Todos os Livros</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>ISBN</th>
                                    <th>Ano</th>
                                    <th>Quantidade</th>
                                    <th>Disponível</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($livro = $result_livros->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $livro['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($livro['titulo']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                                        <td><?php echo htmlspecialchars($livro['isbn']); ?></td>
                                        <td><?php echo $livro['ano']; ?></td>
                                        <td><?php echo $livro['quantidade']; ?></td>
                                        <td>
                                            <?php if ($livro['disponivel'] > 0): ?>
                                                <span class="badge bg-success"><?php echo $livro['disponivel']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                    data-bs-target="#editModal<?php echo $livro['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?eliminar=<?php echo $livro['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Tem certeza que deseja eliminar este livro?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal Editar -->
                                    <div class="modal fade" id="editModal<?php echo $livro['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Livro</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?php echo $livro['id']; ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Título</label>
                                                            <input type="text" class="form-control" name="titulo" 
                                                                   value="<?php echo htmlspecialchars($livro['titulo']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Autor</label>
                                                            <input type="text" class="form-control" name="autor" 
                                                                   value="<?php echo htmlspecialchars($livro['autor']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">ISBN</label>
                                                            <input type="text" class="form-control" name="isbn" 
                                                                   value="<?php echo htmlspecialchars($livro['isbn']); ?>">
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Ano</label>
                                                                <input type="number" class="form-control" name="ano" 
                                                                       value="<?php echo $livro['ano']; ?>" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Quantidade</label>
                                                                <input type="number" class="form-control" name="quantidade" 
                                                                       value="<?php echo $livro['quantidade']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Descrição</label>
                                                            <textarea class="form-control" name="descricao" rows="3"><?php echo htmlspecialchars($livro['descricao']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" name="editar_livro" class="btn btn-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Empréstimos Ativos -->
        <div class="tab-pane fade" id="emprestimos">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bookmark-check"></i> Empréstimos Ativos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Utilizador</th>
                                    <th>Email</th>
                                    <th>Livro</th>
                                    <th>Data Empréstimo</th>
                                    <th>Devolução Prevista</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_emprestimos->num_rows > 0): ?>
                                    <?php while($emp = $result_emprestimos->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($emp['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                            <td><strong><?php echo htmlspecialchars($emp['titulo']); ?></strong></td>
                                            <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($emp['data_devolucao_prevista'])); ?></td>
                                            <td>
                                                <?php 
                                                $atrasado = strtotime($emp['data_devolucao_prevista']) < time();
                                                if ($atrasado): ?>
                                                    <span class="badge bg-danger">Atrasado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Em dia</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-info-circle"></i> Nenhum empréstimo ativo no momento.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab: Adicionar Livro -->
        <div class="tab-pane fade" id="adicionar">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Adicionar Novo Livro</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ano *</label>
                                <input type="number" class="form-control" name="ano" min="1000" max="2099" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Autor *</label>
                                <input type="text" class="form-control" name="autor" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="isbn">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantidade *</label>
                            <input type="number" class="form-control" name="quantidade" min="1" value="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" rows="4" 
                                      placeholder="Breve descrição do livro..."></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" name="adicionar_livro" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> Adicionar Livro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
