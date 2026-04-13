<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? SITE_NAME; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <style>
        .navbar-brand {
            font-weight: bold;
            color: #ff6b35 !important;
        }
        .btn-primary {
            background-color: #ff6b35;
            border-color: #ff6b35;
        }
        .btn-primary:hover {
            background-color: #e55a2b;
            border-color: #e55a2b;
        }
        .card-food {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-food:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .status-pendente { color: #ffc107; }
        .status-preparando { color: #17a2b8; }
        .status-pronto { color: #28a745; }
        .status-entregue { color: #6c757d; }
        .status-cancelado { color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>">
                <i class="fas fa-utensils"></i> Lanches Express
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL ?>/cliente/menu.php">
                            <i class="fas fa-list"></i> Cardápio
                        </a>
                    </li>
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/cliente/pedidos.php">
                                <i class="fas fa-shopping-cart"></i> Meus Pedidos
                            </a>
                        </li>
                        <?php if ($_SESSION['usuario']['tipo'] !== 'cliente'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL ?>/admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL ?>/admin/pedidos.php">
                                    <i class="fas fa-list"></i> Gerenciar Pedidos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL ?>/admin/produtos.php">
                                    <i class="fas fa-utensils"></i> Produtos
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario']['nome']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= SITE_URL ?>/cliente/perfil.php">
                                    <i class="fas fa-user-edit"></i> Meu Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= SITE_URL ?>/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/login.php">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= SITE_URL ?>/cadastro.php">
                                <i class="fas fa-user-plus"></i> Cadastrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['mensagem']['texto']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>