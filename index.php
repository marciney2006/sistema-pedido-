<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Categoria.php';
require_once 'classes/Produto.php';

$titulo = 'Lanches Express - Delivery Rápido e Saboroso';
$categoria = new Categoria();
$produto = new Produto();

// Buscar categorias ativas
$categorias = $categoria->listarAtivas();

// Buscar produtos em destaque (primeiros 8)
$produtos_destaque = array_slice($produto->listarPorCategoria(), 0, 8);

include 'templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="hero-section bg-primary text-white rounded p-5 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold">🍔 Lanches Express</h1>
                    <p class="lead">O melhor delivery de lanches da cidade. Pedidos rápidos, ingredientes frescos e entrega em até 30 minutos!</p>
                    <a href="cliente/menu.php" class="btn btn-light btn-lg">
                        <i class="fas fa-utensils"></i> Ver Cardápio
                    </a>
                </div>
                <div class="col-md-6 text-center">
                    <img src="assets/images/hero-lanches.png" alt="Lanches Express" class="img-fluid" style="max-height: 300px;" onerror="this.style.display='none'">
                    <div style="font-size: 5rem;">🍟</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categorias -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">🍽️ Nossas Categorias</h2>
        <div class="row g-3">
            <?php foreach ($categorias as $cat): ?>
                <div class="col-md-3 col-sm-6">
                    <a href="cliente/menu.php?categoria=<?= $cat['id'] ?>" class="text-decoration-none">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">
                                    <?php
                                    $icones = [
                                        'Lanches' => '🍔',
                                        'Bebidas' => '🥤',
                                        'Sobremesas' => '🍰',
                                        'Porções' => '🍟'
                                    ];
                                    echo $icones[$cat['nome']] ?? '🍽️';
                                    ?>
                                </div>
                                <h5 class="card-title text-dark"><?= htmlspecialchars($cat['nome']) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($cat['descricao'], 0, 60)) ?>...
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Produtos em Destaque -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">⭐ Destaques do Cardápio</h2>
        <div class="row g-4">
            <?php foreach ($produtos_destaque as $prod): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-food h-100">
                        <?php if ($prod['imagem']): ?>
                            <img src="assets/images/produtos/<?= htmlspecialchars($prod['imagem']) ?>"
                                 class="card-img-top" alt="<?= htmlspecialchars($prod['nome']) ?>"
                                 onerror="this.style.display='none'">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($prod['nome']) ?></h5>
                            <p class="card-text flex-grow-1">
                                <?= htmlspecialchars(substr($prod['descricao'], 0, 80)) ?>...
                            </p>
                            <div class="price mb-3">
                                <?= formatarMoeda($prod['preco']) ?>
                            </div>
                            <button class="btn btn-primary w-100"
                                    onclick="adicionarAoCarrinho(<?= $prod['id'] ?>, '<?= addslashes($prod['nome']) ?>', <?= $prod['preco'] ?>, '<?= addslashes($prod['imagem'] ?? '') ?>')">
                                <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="cliente/menu.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-list"></i> Ver Cardápio Completo
            </a>
        </div>
    </div>
</div>

<!-- Como Funciona -->
<div class="row mb-5">
    <div class="col-12">
        <h2 class="text-center mb-4">🚀 Como Funciona</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📱</div>
                <h4>1. Escolha seu Lanche</h4>
                <p>Navegue pelo nosso cardápio completo e adicione os itens ao carrinho.</p>
            </div>
            <div class="col-md-4 text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
                <h4>2. Finalize seu Pedido</h4>
                <p>Revise seu carrinho, escolha a forma de entrega e confirme o pedido.</p>
            </div>
            <div class="col-md-4 text-center">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🚴‍♂️</div>
                <h4>3. Receba em Casa</h4>
                <p>Seu pedido será preparado e entregue rapidamente no conforto da sua casa.</p>
            </div>
        </div>
    </div>
</div>

<!-- Carrinho Flutuante -->
<?php if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] === 'cliente'): ?>
<div class="cart-float" onclick="window.location.href='cliente/carrinho.php'">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count" style="display: none;">0</span>
</div>
<?php endif; ?>

<?php
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

include 'templates/footer.php';
?>