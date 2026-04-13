<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Produto.php';
require_once '../classes/Categoria.php';

$titulo = 'Gerenciar Produtos - Lanches Express';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$produto = new Produto();
$categoria = new Categoria();

$produtos = $produto->listarTodos();
$categorias = $categoria->listarTodas();

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="fas fa-utensils"></i> Gerenciar Produtos
            </h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">
                <i class="fas fa-plus"></i> Novo Produto
            </button>
        </div>
    </div>
</div>

<!-- Lista de Produtos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Produtos (<?= count($produtos) ?>)
                </h5>
            </div>

            <div class="card-body">
                <?php if (empty($produtos)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-utensils fa-3x mb-3"></i>
                        <p>Nenhum produto cadastrado.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">
                            <i class="fas fa-plus"></i> Adicionar Primeiro Produto
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaProdutos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $prod): ?>
                                    <tr>
                                        <td>#<?= str_pad($prod['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <?php if ($prod['imagem']): ?>
                                                <img src="../assets/images/produtos/<?= htmlspecialchars($prod['imagem']) ?>"
                                                     alt="<?= htmlspecialchars($prod['nome']) ?>"
                                                     class="produto-thumb">
                                            <?php else: ?>
                                                <div class="produto-thumb-placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($prod['nome']) ?></strong>
                                            <?php if ($prod['descricao']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($prod['descricao'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($prod['categoria_nome']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge <?= $prod['ativo'] ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $prod['ativo'] ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary"
                                                        onclick="editarProduto(<?= $prod['id'] ?>)"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning"
                                                        onclick="toggleStatus(<?= $prod['id'] ?>, <?= $prod['ativo'] ?>)"
                                                        title="<?= $prod['ativo'] ? 'Desativar' : 'Ativar' ?>">
                                                    <i class="fas fa-<?= $prod['ativo'] ? 'eye-slash' : 'eye' ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                        onclick="deletarProduto(<?= $prod['id'] ?>, '<?= htmlspecialchars($prod['nome']) ?>')"
                                                        title="Deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Produto -->
<div class="modal fade" id="modalProduto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-utensils"></i>
                    <span id="modalTitle">Novo Produto</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProduto" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="produtoId" name="id">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Produto *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoria *</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Preço (R$) *</label>
                            <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                                <label class="form-check-label" for="ativo">Ativo</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Imagem</label>
                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                            <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                            <div id="imagemPreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
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
.produto-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}

.produto-thumb-placeholder {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}
</style>

<script>
let produtoEditando = null;

function editarProduto(id) {
    fetch(`api/produtos.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const produto = data.produto;
                produtoEditando = produto;

                document.getElementById('modalTitle').textContent = 'Editar Produto';
                document.getElementById('produtoId').value = produto.id;
                document.getElementById('nome').value = produto.nome;
                document.getElementById('categoria_id').value = produto.categoria_id;
                document.getElementById('preco').value = produto.preco;
                document.getElementById('ativo').checked = produto.ativo == 1;
                document.getElementById('descricao').value = produto.descricao || '';

                // Preview da imagem atual
                if (produto.imagem) {
                    document.getElementById('previewImg').src = `../assets/images/produtos/${produto.imagem}`;
                    document.getElementById('imagemPreview').style.display = 'block';
                } else {
                    document.getElementById('imagemPreview').style.display = 'none';
                }

                new bootstrap.Modal(document.getElementById('modalProduto')).show();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar produto.');
        });
}

function toggleStatus(id, statusAtual) {
    const acao = statusAtual ? 'desativar' : 'ativar';
    if (!confirm(`Tem certeza que deseja ${acao} este produto?`)) {
        return;
    }

    const novoStatus = statusAtual ? 0 : 1;

    fetch(`api/produtos.php?id=${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ativo: novoStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao alterar status.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}

function deletarProduto(id, nome) {
    if (!confirm(`Tem certeza que deseja deletar o produto "${nome}"?`)) {
        return;
    }

    fetch(`api/produtos.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao deletar produto.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}

// Preview da imagem
document.getElementById('imagem').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagemPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagemPreview').style.display = 'none';
    }
});

// Form submit
document.getElementById('formProduto').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const isEdit = formData.get('id') !== '';

    const url = isEdit ? `api/produtos.php?id=${formData.get('id')}` : 'api/produtos.php';
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
            bootstrap.Modal.getInstance(document.getElementById('modalProduto')).hide();
            location.reload();
        } else {
            alert('Erro ao salvar produto: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
});

// Limpar modal ao fechar
document.getElementById('modalProduto').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formProduto').reset();
    document.getElementById('produtoId').value = '';
    document.getElementById('modalTitle').textContent = 'Novo Produto';
    document.getElementById('imagemPreview').style.display = 'none';
    produtoEditando = null;
});
</script>

<?php include '../templates/footer.php'; ?>