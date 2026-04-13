<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pedido.php';

$titulo = 'Gerenciar Pedidos - Lanches Express';

// Verificar se está logado como admin/funcionario
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['tipo'], ['admin', 'funcionario'])) {
    header("Location: ../login.php");
    exit();
}

$pedido = new Pedido();
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'todos';
$pedidos = $pedido->listarTodos($status_filter);

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-3">
            <i class="fas fa-list"></i> Gerenciar Pedidos
        </h1>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filtrar por Status:</label>
                        <select class="form-select" onchange="filtrarPedidos(this.value)">
                            <option value="todos" <?= $status_filter === 'todos' ? 'selected' : '' ?>>Todos os Pedidos</option>
                            <option value="pendente" <?= $status_filter === 'pendente' ? 'selected' : '' ?>>Pendentes</option>
                            <option value="preparando" <?= $status_filter === 'preparando' ? 'selected' : '' ?>>Preparando</option>
                            <option value="pronto" <?= $status_filter === 'pronto' ? 'selected' : '' ?>>Prontos</option>
                            <option value="entregue" <?= $status_filter === 'entregue' ? 'selected' : '' ?>>Entregues</option>
                            <option value="cancelado" <?= $status_filter === 'cancelado' ? 'selected' : '' ?>>Cancelados</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Buscar por Cliente:</label>
                        <input type="text" class="form-control" id="buscaCliente" placeholder="Nome do cliente...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-outline-secondary w-100" onclick="limparFiltros()">
                            <i class="fas fa-times"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Pedidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart"></i>
                    Pedidos (<?= count($pedidos) ?>)
                </h5>
            </div>

            <div class="card-body">
                <?php if (empty($pedidos)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Nenhum pedido encontrado.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabelaPedidos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Data/Hora</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $ped): ?>
                                    <tr data-cliente="<?= htmlspecialchars(strtolower($ped['cliente_nome'])) ?>">
                                        <td>
                                            <strong>#<?= str_pad($ped['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($ped['cliente_nome']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($ped['cliente_email']) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge status-badge status-<?= $ped['status'] ?>">
                                                <?= ucfirst($ped['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>R$ <?= number_format($ped['valor_total'], 2, ',', '.') ?></strong>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($ped['criado_em'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($ped['criado_em'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="pedido.php?id=<?= $ped['id'] ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <?php if ($ped['status'] === 'pendente'): ?>
                                                    <button class="btn btn-sm btn-outline-warning"
                                                            onclick="atualizarStatus(<?= $ped['id'] ?>, 'preparando')"
                                                            title="Iniciar Preparo">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                <?php elseif ($ped['status'] === 'preparando'): ?>
                                                    <button class="btn btn-sm btn-outline-success"
                                                            onclick="atualizarStatus(<?= $ped['id'] ?>, 'pronto')"
                                                            title="Marcar como Pronto">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php elseif ($ped['status'] === 'pronto'): ?>
                                                    <button class="btn btn-sm btn-outline-info"
                                                            onclick="atualizarStatus(<?= $ped['id'] ?>, 'entregue')"
                                                            title="Marcar como Entregue">
                                                        <i class="fas fa-truck"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($ped['status'] !== 'entregue' && $ped['status'] !== 'cancelado'): ?>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            onclick="atualizarStatus(<?= $ped['id'] ?>, 'cancelado')"
                                                            title="Cancelar Pedido">
                                                        <i class="fas fa-times"></i>
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

<script>
function filtrarPedidos(status) {
    const url = new URL(window.location);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

function limparFiltros() {
    window.location.href = 'pedidos.php';
}

// Busca por cliente
document.getElementById('buscaCliente').addEventListener('input', function() {
    const termo = this.value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaPedidos tbody tr');

    linhas.forEach(linha => {
        const nomeCliente = linha.getAttribute('data-cliente');
        if (nomeCliente.includes(termo)) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }
    });
});

function atualizarStatus(pedidoId, novoStatus) {
    const statusTextos = {
        'preparando': 'iniciar o preparo',
        'pronto': 'marcar como pronto',
        'entregue': 'marcar como entregue',
        'cancelado': 'cancelar'
    };

    if (!confirm(`Tem certeza que deseja ${statusTextos[novoStatus]} este pedido?`)) {
        return;
    }

    fetch(`api/pedidos.php?id=${pedidoId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: novoStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status atualizado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}
</script>

<?php include '../templates/footer.php'; ?>