<?php
session_start();
require_once '../config/database.php';

$titulo = 'Carrinho - Lanches Express';

// Verificar se está logado como cliente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="fas fa-shopping-cart"></i> Seu Carrinho
        </h1>

        <!-- Carrinho vazio -->
        <div id="carrinho-vazio" class="alert alert-info text-center" style="display: none;">
            <i class="fas fa-shopping-cart" style="font-size: 3rem;"></i>
            <h4>Seu carrinho está vazio</h4>
            <p>Adicione alguns produtos deliciosos ao seu carrinho!</p>
            <a href="menu.php" class="btn btn-primary">
                <i class="fas fa-utensils"></i> Ver Cardápio
            </a>
        </div>

        <!-- Itens do carrinho -->
        <div id="itens-carrinho">
            <!-- Carregado via JavaScript -->
        </div>

        <!-- Resumo do pedido -->
        <div id="resumo-pedido" class="card mt-4" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Resumo do Pedido</h5>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="tipo_entrega" class="form-label">Tipo de Entrega</label>
                            <select class="form-select" id="tipo_entrega" name="tipo_entrega">
                                <option value="retirada">Retirada no Local</option>
                                <option value="entrega">Entrega em Domicílio</option>
                            </select>
                        </div>

                        <div class="mb-3" id="endereco-container" style="display: none;">
                            <label for="endereco_entrega" class="form-label">Endereço de Entrega</label>
                            <textarea class="form-control" id="endereco_entrega" name="endereco_entrega"
                                      rows="3" placeholder="Digite seu endereço completo"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações (Opcional)</label>
                            <textarea class="form-control" id="observacoes" name="observacoes"
                                      rows="2" placeholder="Alguma observação especial?"></textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Subtotal: <span id="subtotal" class="float-end">R$ 0,00</span></h6>
                                <h6>Taxa de Entrega: <span id="taxa-entrega" class="float-end">R$ 0,00</span></h6>
                                <hr>
                                <h5>Total: <span id="total" class="float-end text-primary">R$ 0,00</span></h5>
                                <button id="btn-finalizar-pedido" class="btn btn-success w-100 mt-3" type="button">
                                    <i class="fas fa-check"></i> Finalizar Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    carregarCarrinho();

    // Mostrar/esconder campo de endereço baseado no tipo de entrega
    document.getElementById('tipo_entrega').addEventListener('change', function() {
        const enderecoContainer = document.getElementById('endereco-container');
        if (this.value === 'entrega') {
            enderecoContainer.style.display = 'block';
        } else {
            enderecoContainer.style.display = 'none';
        }
        atualizarTotais();
    });

    const btnFinalizar = document.getElementById('btn-finalizar-pedido');
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', finalizarPedido);
    }
});

function carregarCarrinho() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

    if (carrinho.length === 0) {
        document.getElementById('carrinho-vazio').style.display = 'block';
        document.getElementById('resumo-pedido').style.display = 'none';
        return;
    }

    document.getElementById('carrinho-vazio').style.display = 'none';
    document.getElementById('resumo-pedido').style.display = 'block';

    const container = document.getElementById('itens-carrinho');
    container.innerHTML = '';

    carrinho.forEach((item, index) => {
        const itemHtml = `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            ${item.imagem ?
                                `<img src="../assets/images/produtos/${item.imagem}" class="img-fluid rounded" style="max-height: 60px;" onerror="this.style.display='none'">` :
                                '<div style="font-size: 2rem;">🍽️</div>'
                            }
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${item.nome}</h6>
                            ${item.observacoes ? `<small class="text-muted">${item.observacoes}</small>` : ''}
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" onclick="alterarQuantidade(${index}, ${item.quantidade - 1})">-</button>
                                <input type="number" class="form-control text-center" value="${item.quantidade}" min="1"
                                       onchange="alterarQuantidade(${index}, parseInt(this.value))">
                                <button class="btn btn-outline-secondary" onclick="alterarQuantidade(${index}, ${item.quantidade + 1})">+</button>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <strong>R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</strong>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-outline-danger btn-sm" onclick="removerDoCarrinho(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    });

    atualizarTotais();
}

function atualizarTotais() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    const tipoEntrega = document.getElementById('tipo_entrega').value;

    let subtotal = 0;
    carrinho.forEach(item => {
        subtotal += item.preco * item.quantidade;
    });

    const taxaEntrega = tipoEntrega === 'entrega' ? 5.00 : 0;
    const total = subtotal + taxaEntrega;

    document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('taxa-entrega').textContent = `R$ ${taxaEntrega.toFixed(2).replace('.', ',')}`;
    document.getElementById('total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

function alterarQuantidade(index, novaQuantidade) {
    if (novaQuantidade < 1) return;

    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho[index].quantidade = novaQuantidade;
    localStorage.setItem('carrinho', JSON.stringify(carrinho));

    carregarCarrinho();
    window.carrinho.atualizarContador();
}

function removerDoCarrinho(index) {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho.splice(index, 1);
    localStorage.setItem('carrinho', JSON.stringify(carrinho));

    carregarCarrinho();
    window.carrinho.atualizarContador();
}

const API_PEDIDOS_URL = '<?= SITE_URL ?>/cliente/api/pedidos.php';
const PEDIDO_DETALHE_BASE_URL = '<?= SITE_URL ?>/cliente/pedido.php';

function finalizarPedido() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

    if (carrinho.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }

    const tipoEntrega = document.getElementById('tipo_entrega').value;
    const enderecoEntrega = document.getElementById('endereco_entrega').value;
    const observacoes = document.getElementById('observacoes').value;

    if (tipoEntrega === 'entrega' && !enderecoEntrega.trim()) {
        alert('Por favor, informe o endereço de entrega.');
        document.getElementById('endereco_entrega').focus();
        return;
    }

    // Enviar pedido para o servidor
    const itensParaEnvio = carrinho.map(item => ({
        produto_id: item.produto_id ?? item.id,
        quantidade: item.quantidade,
        observacoes: item.observacoes || null
    }));

    const payload = {
        itens: itensParaEnvio,
        tipo_entrega: tipoEntrega,
        endereco_entrega: enderecoEntrega,
        observacoes: observacoes
    };

    fetch(API_PEDIDOS_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Limpar carrinho
            localStorage.removeItem('carrinho');
            window.carrinho.atualizarContador();

            // Redirecionar para página de sucesso
            window.location.href = `${PEDIDO_DETALHE_BASE_URL}?id=${data.pedido_id}`;
        } else {
            alert('Erro ao finalizar pedido: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro ao finalizar pedido:', error);
        alert('Erro ao conectar com o servidor. Tente novamente.');
    });
}

// Garante que o botão da página use esta implementação, não o fallback global.
window.finalizarPedido = finalizarPedido;
</script>

<?php include '../templates/footer.php'; ?>