<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Pedido.php';

$titulo = 'Detalhes do Pedido - Lanches Express';

// Verificar se está logado como cliente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$pedido_id = intval($_GET['id'] ?? 0);
$pedido = new Pedido();
$detalhes = $pedido->buscarPorId($pedido_id);

// Verificar se o pedido existe e pertence ao usuário
if (!$detalhes || $detalhes['usuario_id'] != $_SESSION['usuario']['id']) {
    header("Location: pedidos.php");
    exit();
}

include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="pedidos.php">Meus Pedidos</a></li>
                <li class="breadcrumb-item active">Pedido #<?= str_pad($detalhes['id'], 4, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-receipt"></i>
                        Pedido #<?= str_pad($detalhes['id'], 4, '0', STR_PAD_LEFT) ?>
                    </h4>
                    <span class="badge status-badge status-<?= $detalhes['status'] ?> fs-6">
                        <?= ucfirst($detalhes['status']) ?>
                    </span>
                </div>
            </div>

            <div class="card-body">
                <!-- Barra de progresso do pedido -->
                <?php
                $status_steps = ['pendente', 'preparando', 'pronto', 'entregue'];
                $status_labels = ['Pedido\nRecebido', 'Em\nPreparo', 'Pronto!', 'Entregue'];
                $status_icons  = ['fa-check-circle', 'fa-fire', 'fa-bell', 'fa-truck'];
                $status_colors = ['primary', 'warning', 'success', 'info'];
                $current_idx   = array_search($detalhes['status'], $status_steps);
                if ($current_idx === false) $current_idx = -1; // cancelado
                ?>
                <?php if ($detalhes['status'] !== 'cancelado'): ?>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start position-relative">
                        <!-- linha de progresso atrás dos ícones -->
                        <div style="position:absolute;top:22px;left:10%;right:10%;height:4px;background:#e9ecef;z-index:0;">
                            <div style="width:<?= $current_idx >= 1 ? min(100, ($current_idx / (count($status_steps)-1)) * 100) : 0 ?>%;height:100%;background:#0d6efd;transition:width .5s;"></div>
                        </div>
                        <?php foreach ($status_steps as $i => $step): ?>
                            <?php $done = $i <= $current_idx; ?>
                            <div class="text-center flex-fill" style="z-index:1;">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                     style="width:44px;height:44px;background:<?= $done ? '#0d6efd' : '#e9ecef' ?>;color:<?= $done ? '#fff' : '#adb5bd' ?>;">
                                    <i class="fas <?= $status_icons[$i] ?>"></i>
                                </div>
                                <div style="font-size:.75rem;white-space:pre-line;color:<?= $done ? '#0d6efd' : '#adb5bd' ?>;font-weight:<?= $i === $current_idx ? '700' : '400' ?>;">
                                    <?= $status_labels[$i] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php
                $mensagens = [
                    'pendente'   => ['🕐', 'Aguardando confirmação', 'Seu pedido foi recebido e está aguardando o restaurante iniciar o preparo.'],
                    'preparando' => ['👨‍🍳', 'Em preparo!', 'O restaurante está preparando seu pedido. Aguarde!'],
                    'pronto'     => ['🔔', 'Pedido pronto!', 'Seu pedido está pronto! Pode retirar ou aguardar a entrega.'],
                    'entregue'   => ['✅', 'Pedido entregue!', 'Seu pedido foi entregue. Bom apetite!'],
                ];
                [$icon, $titulo_status, $desc_status] = $mensagens[$detalhes['status']] ?? ['📋', 'Status desconhecido', ''];
                ?>
                <div class="alert alert-<?= ['pendente'=>'secondary','preparando'=>'warning','pronto'=>'success','entregue'=>'info'][$detalhes['status']] ?? 'secondary' ?> d-flex align-items-center gap-3 mb-0">
                    <span style="font-size:2rem;"><?= $icon ?></span>
                    <div>
                        <strong><?= $titulo_status ?></strong><br>
                        <small><?= $desc_status ?></small>
                    </div>
                    <?php if (in_array($detalhes['status'], ['pendente', 'preparando', 'pronto'])): ?>
                        <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-danger d-flex align-items-center gap-3 mb-0">
                    <span style="font-size:2rem;">❌</span>
                    <div><strong>Pedido cancelado</strong><br><small>Este pedido foi cancelado.</small></div>
                </div>
                <?php endif; ?>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar"></i> Data do Pedido</h6>
                        <p><?= date('d/m/Y \à\s H:i', strtotime($detalhes['criado_em'])) ?></p>

                        <h6><i class="fas fa-truck"></i> Tipo de Entrega</h6>
                        <p>
                            <?= $detalhes['tipo_entrega'] === 'entrega' ? 'Entrega em Domicílio' : 'Retirada no Local' ?>
                            <?php if ($detalhes['tipo_entrega'] === 'entrega' && $detalhes['endereco_entrega']): ?>
                                <br><small class="text-muted"><?= nl2br(htmlspecialchars($detalhes['endereco_entrega'])) ?></small>
                            <?php endif; ?>
                        </p>

                        <?php if ($detalhes['observacoes']): ?>
                            <h6><i class="fas fa-sticky-note"></i> Observações</h6>
                            <p><?= nl2br(htmlspecialchars($detalhes['observacoes'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <h6><i class="fas fa-user"></i> Dados do Cliente</h6>
                        <p>
                            <strong><?= htmlspecialchars($detalhes['cliente_nome']) ?></strong><br>
                            <small class="text-muted">
                                <?= htmlspecialchars($detalhes['cliente_email']) ?><br>
                                <?= htmlspecialchars($detalhes['cliente_telefone']) ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Itens do Pedido</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Quantidade</th>
                                <th class="text-end">Preço Unitário</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalhes['itens'] as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
                                        <?php if ($item['observacoes']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($item['observacoes']) ?></small>
                                        <?php endif; ?>
                                        <?php if ($item['produto_descricao']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($item['produto_descricao']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $item['quantidade'] ?></td>
                                    <td class="text-end">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                                    <td class="text-end">
                                        <strong>R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total do Pedido:</strong></td>
                                <td class="text-end">
                                    <strong class="text-primary fs-5">
                                        R$ <?= number_format($detalhes['valor_total'], 2, ',', '.') ?>
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="mt-4 text-center">
            <?php if ($detalhes['status'] === 'pendente'): ?>
                <button class="btn btn-danger me-2" onclick="cancelarPedido(<?= $detalhes['id'] ?>)">
                    <i class="fas fa-times"></i> Cancelar Pedido
                </button>
            <?php endif; ?>

            <a href="pedidos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar aos Pedidos
            </a>

            <a href="menu.php" class="btn btn-primary ms-2">
                <i class="fas fa-cart-plus"></i> Fazer Novo Pedido
            </a>
        </div>
    </div>
</div>

<script>
function cancelarPedido(pedidoId) {
    if (!confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
        return;
    }

    fetch(`api/pedidos.php?id=${pedidoId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pedido cancelado com sucesso!');
            window.location.href = 'pedidos.php';
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