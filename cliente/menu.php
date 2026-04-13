<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Categoria.php';
require_once '../classes/Produto.php';

$titulo = 'Cardápio - Lanches Express';

// Verificar se está logado como cliente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'cliente') {
    header("Location: ../login.php");
    exit();
}

$categoria = new Categoria();
$produto = new Produto();

// Buscar categoria específica ou todas
$categoria_atual = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Buscar categorias
$categorias = $categoria->listarAtivas();

// Buscar produtos
if ($categoria_atual) {
    $produtos = $produto->listarPorCategoria($categoria_atual);
} elseif ($busca) {
    // Busca por nome (simples, pode ser melhorado)
    $produtos = array_filter($produto->listarPorCategoria(), function($p) use ($busca) {
        return stripos($p['nome'], $busca) !== false || stripos($p['descricao'], $busca) !== false;
    });
} else {
    $produtos = $produto->listarPorCategoria();
}

include '../templates/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-3">
            <i class="fas fa-utensils"></i> Nosso Cardápio
        </h1>

        <!-- Barra de busca -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="busca"
                               placeholder="Buscar lanches, bebidas..."
                               value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="menu.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtros por categoria -->
        <div class="mb-4">
            <h5>Filtros:</h5>
            <div class="d-flex flex-wrap gap-2">
                <a href="menu.php" class="btn btn-outline-primary <?= !$categoria_atual && !$busca ? 'active' : '' ?>">
                    <i class="fas fa-th-list"></i> Todos
                </a>
                <?php foreach ($categorias as $cat): ?>
                    <a href="menu.php?categoria=<?= $cat['id'] ?>"
                       class="btn btn-outline-primary <?= $categoria_atual == $cat['id'] ? 'active' : '' ?>">
                        <?php
                        $icones = [
                            'Lanches' => '🍔',
                            'Bebidas' => '🥤',
                            'Sobremesas' => '🍰',
                            'Porções' => '🍟'
                        ];
                        echo $icones[$cat['nome']] ?? '🍽️';
                        ?>
                        <?= htmlspecialchars($cat['nome']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Produtos -->
<div class="row g-4">
    <?php if (empty($produtos)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-search"></i>
                <h4>Nenhum produto encontrado</h4>
                <p>Tente ajustar sua busca ou filtro.</p>
                <a href="menu.php" class="btn btn-primary">Ver todos os produtos</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($produtos as $prod): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card card-food h-100 <?= !$prod['disponivel'] ? 'produto-indisponivel' : '' ?>">
                    <?php if ($prod['imagem']): ?>
                        <img src="../assets/images/produtos/<?= htmlspecialchars($prod['imagem']) ?>"
                             class="card-img-top" alt="<?= htmlspecialchars($prod['nome']) ?>"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($prod['nome']) ?></h5>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($prod['categoria_nome']) ?>
                            </span>
                        </div>

                        <p class="card-text flex-grow-1">
                            <?= htmlspecialchars($prod['descricao']) ?>
                        </p>

                        <div class="price mb-3">
                            R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                        </div>

                        <?php if ($prod['disponivel']): ?>
                            <button class="btn btn-primary w-100"
                                    onclick="adicionarAoCarrinho(<?= $prod['id'] ?>, '<?= addslashes($prod['nome']) ?>', <?= $prod['preco'] ?>, '<?= addslashes($prod['imagem'] ?? '') ?>')">
                                <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-ban"></i> Indisponível
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Carrinho Flutuante -->
<div class="cart-float" onclick="window.location.href='carrinho.php'">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count" style="display: none;">0</span>
</div>

<?php include '../templates/footer.php'; ?>