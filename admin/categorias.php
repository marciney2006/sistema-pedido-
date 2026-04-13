<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Categoria.php';

$titulo = 'Gerenciar Categorias - Lanches Express';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$categoria = new Categoria();
$categorias = $categoria->listarTodas();

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="fas fa-tags"></i> Gerenciar Categorias
            </h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                <i class="fas fa-plus"></i> Nova Categoria
            </button>
        </div>
    </div>
</div>

<!-- Lista de Categorias -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Categorias (<?= count($categorias) ?>)
                </h5>
            </div>

            <div class="card-body">
                <?php if (empty($categorias)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-tags fa-3x mb-3"></i>
                        <p>Nenhuma categoria cadastrada.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                            <i class="fas fa-plus"></i> Adicionar Primeira Categoria
                        </button>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($categorias as $cat): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card categoria-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    <?= htmlspecialchars($cat['nome']) ?>
                                                </h5>
                                                <?php if ($cat['descricao']): ?>
                                                    <p class="card-text text-muted small mb-2">
                                                        <?= htmlspecialchars($cat['descricao']) ?>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="categoria-stats">
                                                    <small class="text-muted">
                                                        <i class="fas fa-utensils me-1"></i>
                                                        <?= $cat['total_produtos'] ?? 0 ?> produto(s)
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           onclick="editarCategoria(<?= $cat['id'] ?>)">
                                                            <i class="fas fa-edit me-2"></i>Editar
                                                        </a>
                                                    </li>
                                                    <?php if (($cat['total_produtos'] ?? 0) === 0): ?>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                               onclick="deletarCategoria(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nome']) ?>')">
                                                                <i class="fas fa-trash me-2"></i>Deletar
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Categoria -->
<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tag"></i>
                    <span id="modalTitle">Nova Categoria</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCategoria">
                <div class="modal-body">
                    <input type="hidden" id="categoriaId" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nome da Categoria *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                        <div class="form-text">Ex: Lanches, Bebidas, Sobremesas</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"
                                  placeholder="Descrição opcional da categoria..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.categoria-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.categoria-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.categoria-stats {
    margin-top: 10px;
}
</style>

<script>
function editarCategoria(id) {
    fetch(`api/categorias.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const categoria = data.categoria;

                document.getElementById('modalTitle').textContent = 'Editar Categoria';
                document.getElementById('categoriaId').value = categoria.id;
                document.getElementById('nome').value = categoria.nome;
                document.getElementById('descricao').value = categoria.descricao || '';

                new bootstrap.Modal(document.getElementById('modalCategoria')).show();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar categoria.');
        });
}

function deletarCategoria(id, nome) {
    if (!confirm(`Tem certeza que deseja deletar a categoria "${nome}"?\n\nAtenção: Esta ação não pode ser desfeita.`)) {
        return;
    }

    fetch(`api/categorias.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao deletar categoria: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}

// Form submit
document.getElementById('formCategoria').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const isEdit = formData.get('id') !== '';

    const url = isEdit ? `api/categorias.php?id=${formData.get('id')}` : 'api/categorias.php';
    const method = isEdit ? 'PUT' : 'POST';

    // Se não é edição, remove o campo id
    if (!isEdit) {
        formData.delete('id');
    }

    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
            location.reload();
        } else {
            alert('Erro ao salvar categoria: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
});

// Limpar modal ao fechar
document.getElementById('modalCategoria').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formCategoria').reset();
    document.getElementById('categoriaId').value = '';
    document.getElementById('modalTitle').textContent = 'Nova Categoria';
});
</script>

<?php include '../templates/footer.php'; ?>