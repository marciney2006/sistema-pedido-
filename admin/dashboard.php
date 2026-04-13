<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pedido.php';

$titulo = 'Dashboard Administrativo - Lanches Express';

// Verificar se está logado como admin/funcionario
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['tipo'], ['admin', 'funcionario'])) {
    header("Location: ../login.php");
    exit();
}

$pedido = new Pedido();
$estatisticas = $pedido->getEstatisticas();
$pedidos_recentes = $pedido->listarTodos();

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-3">
            <i class="fas fa-tachometer-alt"></i> Dashboard Administrativo
        </h1>
        <p class="text-muted">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>!</p>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card text-white">
            <div class="card-body">
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stats-value"><?= $estatisticas['pedidos_hoje'] ?></div>
                <div class="stats-label">Pedidos Hoje</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-white">
            <div class="card-body">
                <div class="stats-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-value">R$ <?= number_format($estatisticas['valor_hoje'], 2, ',', '.') ?></div>
                <div class="stats-label">Faturamento Hoje</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-white">
            <div class="card-body">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-value"><?= $estatisticas['pedidos_pendentes'] ?></div>
                <div class="stats-label">Pedidos Pendentes</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stats-card text-white">
            <div class="card-body">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-value">
                    <?php
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'cliente'");
                    $stmt->execute();
                    echo $stmt->fetch()['total'];
                    ?>
                </div>
                <div class="stats-label">Total de Clientes</div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="pedidos.php" class="btn btn-primary w-100">
                            <i class="fas fa-list"></i><br>Gerenciar Pedidos
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="produtos.php" class="btn btn-success w-100">
                            <i class="fas fa-utensils"></i><br>Gerenciar Produtos
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="categorias.php" class="btn btn-info w-100">
                            <i class="fas fa-tags"></i><br>Gerenciar Categorias
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="usuarios.php" class="btn btn-warning w-100">
                            <i class="fas fa-users"></i><br>Gerenciar Usuários
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Recentes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Pedidos Recentes</h5>
                <a href="pedidos.php" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>

            <div class="card-body">
                <?php if (empty($pedidos_recentes)): ?>
                    <div class="text-center text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Nenhum pedido ainda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($pedidos_recentes, 0, 10) as $ped): ?>
                                    <tr>
                                        <td>#<?= str_pad($ped['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= htmlspecialchars($ped['cliente_nome']) ?></td>
                                        <td>
                                            <span class="badge status-badge status-<?= $ped['status'] ?>">
                                                <?= ucfirst($ped['status']) ?>
                                            </span>
                                        </td>
                                        <td>R$ <?= number_format($ped['valor_total'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m H:i', strtotime($ped['criado_em'])) ?></td>
                                        <td>
                                            <a href="pedido.php?id=<?= $ped['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($ped['status'] !== 'entregue' && $ped['status'] !== 'cancelado'): ?>
                                                <button class="btn btn-sm btn-outline-success ms-1"
                                                        onclick="atualizarStatus(<?= $ped['id'] ?>, 'pronto')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
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
function atualizarStatus(pedidoId, novoStatus) {
    if (!confirm('Tem certeza que deseja alterar o status deste pedido?')) {
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
            alert('Erro ao atualizar status.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}
</script>

<?php include '../templates/footer.php'; ?>