<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pedido.php';

$titulo = 'Meus Pedidos - Lanches Express';

// Verificar se está logado como cliente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$pedido = new Pedido();
$pedidos = $pedido->listarPorUsuario($_SESSION['usuario']['id']);

include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="fas fa-receipt"></i> Meus Pedidos
        </h1>

        <?php if (empty($pedidos)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-shopping-cart" style="font-size: 3rem;"></i>
                <h4>Você ainda não fez nenhum pedido</h4>
                <p>Que tal experimentar nossos deliciosos lanches?</p>
                <a href="menu.php" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Ver Cardápio
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($pedidos as $ped): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">
                                        Pedido #<?= str_pad($ped['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </h5>
                                    <span class="badge status-badge status-<?= $ped['status'] ?>">
                                        <?= ucfirst($ped['status']) ?>
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d/m/Y H:i', strtotime($ped['criado_em'])) ?>
                                    </small>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-truck"></i>
                                        <?= $ped['tipo_entrega'] === 'entrega' ? 'Entrega' : 'Retirada' ?>
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <strong class="text-primary">
                                        R$ <?= number_format($ped['valor_total'], 2, ',', '.') ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= $ped['total_itens'] ?> item<?= $ped['total_itens'] > 1 ? 's' : '' ?>
                                    </small>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="pedido.php?id=<?= $ped['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </a>

                                    <?php if ($ped['status'] === 'pendente'): ?>
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="cancelarPedido(<?= $ped['id'] ?>)">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function cancelarPedido(pedidoId) {
    if (!confirm('Tem certeza que deseja cancelar este pedido?')) {
        return;
    }

    fetch(`api/pedidos.php?id=${pedidoId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pedido cancelado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao cancelar pedido. Tente novamente.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao conectar com o servidor.');
    });
}
</script>

<?php include '../templates/footer.php'; ?>