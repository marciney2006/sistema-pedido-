<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pedido.php';

$titulo = 'Detalhes do Pedido - Lanches Express';

// Verificar se está logado como admin/funcionario
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['tipo'], ['admin', 'funcionario'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pedidos.php");
    exit();
}

$pedido = new Pedido();
$pedido_detalhes = $pedido->buscarPorId($_GET['id']);

if (!$pedido_detalhes) {
    header("Location: pedidos.php");
    exit();
}

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="pedidos.php">Pedidos</a></li>
                <li class="breadcrumb-item active">Pedido #<?= str_pad($pedido_detalhes['id'], 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">
                <i class="fas fa-receipt"></i> Pedido #<?= str_pad($pedido_detalhes['id'], 4, '0', STR_PAD_LEFT) ?>
            </h1>
            <div>
                <a href="pedidos.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações do Pedido -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informações do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <span class="badge status-badge status-<?= $pedido_detalhes['status'] ?> ms-2">
                            <?= ucfirst($pedido_detalhes['status']) ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Data/Hora:</strong>
                        <span class="ms-2">
                            <?= date('d/m/Y \à\s H:i', strtotime($pedido_detalhes['criado_em'])) ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Cliente:</strong>
                        <span class="ms-2"><?= htmlspecialchars($pedido_detalhes['cliente_nome']) ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <span class="ms-2"><?= htmlspecialchars($pedido_detalhes['cliente_email']) ?></span>
                    </div>
                    <?php if ($pedido_detalhes['observacoes']): ?>
                        <div class="col-12">
                            <strong>Observações:</strong>
                            <div class="ms-2 mt-1 p-2 bg-light rounded">
                                <?= nl2br(htmlspecialchars($pedido_detalhes['observacoes'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-utensils"></i> Itens do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedido_detalhes['itens'] as $item): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
                                            <?php if ($item['observacoes']): ?>
                                                <br><small class="text-muted">Obs: <?= htmlspecialchars($item['observacoes']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $item['quantidade'] ?>x</td>
                                    <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                    <td class="text-end">
                                        <strong>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end">
                                    <strong class="text-primary fs-5">
                                        R$ <?= number_format($pedido_detalhes['valor_total'], 2, ',', '.') ?>
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações do Pedido -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs"></i> Ações do Pedido</h5>
            </div>
            <div class="card-body">
                <?php if ($pedido_detalhes['status'] === 'pendente'): ?>
                    <button class="btn btn-warning w-100 mb-2"
                            onclick="atualizarStatus(<?= $pedido_detalhes['id'] ?>, 'preparando')">
                        <i class="fas fa-play"></i> Iniciar Preparo
                    </button>
                <?php elseif ($pedido_detalhes['status'] === 'preparando'): ?>
                    <button class="btn btn-success w-100 mb-2"
                            onclick="atualizarStatus(<?= $pedido_detalhes['id'] ?>, 'pronto')">
                        <i class="fas fa-check"></i> Marcar como Pronto
                    </button>
                <?php elseif ($pedido_detalhes['status'] === 'pronto'): ?>
                    <button class="btn btn-info w-100 mb-2"
                            onclick="atualizarStatus(<?= $pedido_detalhes['id'] ?>, 'entregue')">
                        <i class="fas fa-truck"></i> Marcar como Entregue
                    </button>
                <?php endif; ?>

                <?php if ($pedido_detalhes['status'] !== 'entregue' && $pedido_detalhes['status'] !== 'cancelado'): ?>
                    <button class="btn btn-danger w-100 mb-2"
                            onclick="atualizarStatus(<?= $pedido_detalhes['id'] ?>, 'cancelado')">
                        <i class="fas fa-times"></i> Cancelar Pedido
                    </button>
                <?php endif; ?>

                <hr>

                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir Pedido
                    </button>
                    <button class="btn btn-outline-secondary" onclick="copiarParaAreaTransferencia()">
                        <i class="fas fa-copy"></i> Copiar Detalhes
                    </button>
                </div>
            </div>
        </div>

        <!-- Timeline do Status -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item <?= $pedido_detalhes['status'] === 'pendente' || $pedido_detalhes['status'] === 'preparando' || $pedido_detalhes['status'] === 'pronto' || $pedido_detalhes['status'] === 'entregue' ? 'completed' : '' ?>">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Pedido Recebido</h6>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($pedido_detalhes['criado_em'])) ?>
                            </small>
                        </div>
                    </div>

                    <div class="timeline-item <?= $pedido_detalhes['status'] === 'preparando' || $pedido_detalhes['status'] === 'pronto' || $pedido_detalhes['status'] === 'entregue' ? 'completed' : '' ?>">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Em Preparo</h6>
                            <small class="text-muted">Aguardando confirmação</small>
                        </div>
                    </div>

                    <div class="timeline-item <?= $pedido_detalhes['status'] === 'pronto' || $pedido_detalhes['status'] === 'entregue' ? 'completed' : '' ?>">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Pronto para Entrega</h6>
                            <small class="text-muted">Aguardando retirada</small>
                        </div>
                    </div>

                    <div class="timeline-item <?= $pedido_detalhes['status'] === 'entregue' ? 'completed' : '' ?>">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-0">Entregue</h6>
                            <small class="text-muted">Pedido finalizado</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item.completed .timeline-marker {
    background-color: #28a745 !important;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
    background: #6c757d;
}

.timeline-content h6 {
    margin-bottom: 2px;
}
</style>

<script>
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

function copiarParaAreaTransferencia() {
    const detalhes = `
Pedido #${'<?= str_pad($pedido_detalhes['id'], 4, '0', STR_PAD_LEFT) ?>'}
Cliente: ${'<?= htmlspecialchars($pedido_detalhes['cliente_nome']) ?>'}
Status: ${'<?= ucfirst($pedido_detalhes['status']) ?>'}
Data: ${'<?= date('d/m/Y H:i', strtotime($pedido_detalhes['criado_em'])) ?>'}
Total: R$ ${'<?= number_format($pedido_detalhes['valor_total'], 2, ',', '.') ?>'}

Itens:
<?php foreach ($pedido_detalhes['itens'] as $item): ?>
- ${'<?= htmlspecialchars($item['produto_nome']) ?>'} (<?= $item['quantidade'] ?>x) - R$ ${'<?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?>'}
<?php endforeach; ?>
    `.trim();

    navigator.clipboard.writeText(detalhes).then(() => {
        alert('Detalhes copiados para a área de transferência!');
    }).catch(() => {
        // Fallback para navegadores antigos
        const textArea = document.createElement('textarea');
        textArea.value = detalhes;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Detalhes copiados para a área de transferência!');
    });
}
</script>

<?php include '../templates/footer.php'; ?>