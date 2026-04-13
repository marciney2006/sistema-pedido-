<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Usuario.php';

$titulo = 'Gerenciar Usuários - Lanches Express';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$usuario = new Usuario();
$usuarios = $usuario->listarTodos();

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="fas fa-users"></i> Gerenciar Usuários
            </h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                <i class="fas fa-plus"></i> Novo Usuário
            </button>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="stat-icon text-primary mb-2">
                    <i class="fas fa-user fa-2x"></i>
                </div>
                <h4 class="card-title">
                    <?php
                    $total_clientes = count(array_filter($usuarios, function($u) { return $u['tipo'] === 'cliente'; }));
                    echo $total_clientes;
                    ?>
                </h4>
                <p class="card-text text-muted">Clientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="stat-icon text-warning mb-2">
                    <i class="fas fa-user-tie fa-2x"></i>
                </div>
                <h4 class="card-title">
                    <?php
                    $total_funcionarios = count(array_filter($usuarios, function($u) { return $u['tipo'] === 'funcionario'; }));
                    echo $total_funcionarios;
                    ?>
                </h4>
                <p class="card-text text-muted">Funcionários</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="stat-icon text-danger mb-2">
                    <i class="fas fa-crown fa-2x"></i>
                </div>
                <h4 class="card-title">
                    <?php
                    $total_admins = count(array_filter($usuarios, function($u) { return $u['tipo'] === 'admin'; }));
                    echo $total_admins;
                    ?>
                </h4>
                <p class="card-text text-muted">Administradores</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Usuários (<?= count($usuarios) ?>)
                </h5>
            </div>

            <div class="card-body">
                <?php if (empty($usuarios)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Nenhum usuário cadastrado.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                            <i class="fas fa-plus"></i> Adicionar Primeiro Usuário
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaUsuarios">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $user): ?>
                                    <tr>
                                        <td>#<?= str_pad($user['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($user['nome']) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <span class="badge tipo-badge tipo-<?= $user['tipo'] ?>">
                                                <?php
                                                $tipos = [
                                                    'cliente' => 'Cliente',
                                                    'funcionario' => 'Funcionário',
                                                    'admin' => 'Admin'
                                                ];
                                                echo $tipos[$user['tipo']] ?? $user['tipo'];
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $user['ativo'] ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $user['ativo'] ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($user['criado_em'])) ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary"
                                                        onclick="editarUsuario(<?= $user['id'] ?>)"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning"
                                                        onclick="toggleStatus(<?= $user['id'] ?>, <?= $user['ativo'] ?>)"
                                                        title="<?= $user['ativo'] ? 'Desativar' : 'Ativar' ?>">
                                                    <i class="fas fa-<?= $user['ativo'] ? 'eye-slash' : 'eye' ?>"></i>
                                                </button>
                                                <?php if ($user['tipo'] !== 'admin' || $_SESSION['usuario']['id'] !== $user['id']): ?>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            onclick="deletarUsuario(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nome']) ?>')"
                                                            title="Deletar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Modal Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user"></i>
                    <span id="modalTitle">Novo Usuário</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" id="usuarioId" name="id">

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="cliente">Cliente</option>
                                <option value="funcionario">Funcionário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                                <label class="form-check-label" for="ativo">Ativo</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" id="senhaLabel">Senha *</label>
                            <input type="password" class="form-control" id="senha" name="senha">
                            <div class="form-text">Mínimo 6 caracteres</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar Senha *</label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha">
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
.stat-icon {
    opacity: 0.7;
}

.tipo-badge {
    font-size: 0.8em;
}

.tipo-cliente {
    background-color: #007bff;
}

.tipo-funcionario {
    background-color: #ffc107;
    color: #000;
}

.tipo-admin {
    background-color: #dc3545;
}
</style>

<script>
let usuarioEditando = null;

function editarUsuario(id) {
    fetch(`api/usuarios.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.usuario;
                usuarioEditando = usuario;

                document.getElementById('modalTitle').textContent = 'Editar Usuário';
                document.getElementById('usuarioId').value = usuario.id;
                document.getElementById('nome').value = usuario.nome;
                document.getElementById('email').value = usuario.email;
                document.getElementById('tipo').value = usuario.tipo;
                document.getElementById('ativo').checked = usuario.ativo == 1;

                // Para edição, senha é opcional
                document.getElementById('senhaLabel').textContent = 'Nova Senha (deixe em branco para manter)';
                document.getElementById('senha').required = false;
                document.getElementById('confirmar_senha').required = false;

                new bootstrap.Modal(document.getElementById('modalUsuario')).show();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar usuário.');
        });
}

function toggleStatus(id, statusAtual) {
    const acao = statusAtual ? 'desativar' : 'ativar';
    if (!confirm(`Tem certeza que deseja ${acao} este usuário?`)) {
        return;
    }

    const novoStatus = statusAtual ? 0 : 1;

    fetch(`api/usuarios.php?id=${id}`, {
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

function deletarUsuario(id, nome) {
    if (!confirm(`Tem certeza que deseja deletar o usuário "${nome}"?\n\nAtenção: Esta ação não pode ser desfeita.`)) {
        return;
    }

    fetch(`api/usuarios.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao deletar usuário.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}

// Validação de senha
document.getElementById('confirmar_senha').addEventListener('input', function() {
    const senha = document.getElementById('senha').value;
    const confirmar = this.value;

    if (senha !== confirmar) {
        this.setCustomValidity('As senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});

// Form submit
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const isEdit = formData.get('id') !== '';

    // Para edição, se senha estiver vazia, remove do form
    if (isEdit && !formData.get('senha')) {
        formData.delete('senha');
        formData.delete('confirmar_senha');
    }

    const url = isEdit ? `api/usuarios.php?id=${formData.get('id')}` : 'api/usuarios.php';
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
            bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
            location.reload();
        } else {
            alert('Erro ao salvar usuário: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
});

// Limpar modal ao fechar
document.getElementById('modalUsuario').addEventListener('hidden.bs.modal', function() {
    document.getElementById('formUsuario').reset();
    document.getElementById('usuarioId').value = '';
    document.getElementById('modalTitle').textContent = 'Novo Usuário';
    document.getElementById('senhaLabel').textContent = 'Senha *';
    document.getElementById('senha').required = true;
    document.getElementById('confirmar_senha').required = true;
    usuarioEditando = null;
});
</script>

<?php include '../templates/footer.php'; ?>