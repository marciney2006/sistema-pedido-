<?php
session_start();

// Se já estiver logado, redirecionar
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'classes/Usuario.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuario();
    $dados = [
        'nome' => trim($_POST['nome']),
        'email' => trim($_POST['email']),
        'senha' => $_POST['senha'],
        'telefone' => trim($_POST['telefone'] ?? ''),
        'tipo' => 'cliente'
    ];

    // Validações básicas
    if (strlen($dados['nome']) < 2) {
        $erro = "Nome deve ter pelo menos 2 caracteres!";
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido!";
    } elseif (strlen($dados['senha']) < 6) {
        $erro = "Senha deve ter pelo menos 6 caracteres!";
    } elseif ($dados['senha'] !== $_POST['confirmar_senha']) {
        $erro = "As senhas não coincidem!";
    } else {
        // Tentar cadastrar
        if ($usuario->cadastrar($dados)) {
            $sucesso = "Conta criada com sucesso! Você pode fazer login agora.";
        } else {
            $erro = "Erro ao criar conta. Email já cadastrado!";
        }
    }
}

$titulo = 'Cadastro - Lanches Express';
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🎉</div>
                    <h2 class="card-title">Crie sua conta</h2>
                    <p class="text-muted">Junte-se a nós e faça seus pedidos de forma rápida e fácil</p>
                </div>

                <?php if ($erro): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $erro ?>
                    </div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?= $sucesso ?>
                        <br><a href="login.php" class="alert-link">Clique aqui para fazer login</a>
                    </div>
                <?php endif; ?>

                <form method="POST" data-validate="true">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">
                                <i class="fas fa-user"></i> Nome Completo *
                            </label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                   placeholder="Seu nome completo" required
                                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">
                                <i class="fas fa-phone"></i> Telefone
                            </label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                   placeholder="(11) 99999-9999"
                                   value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="seu@email.com" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">
                                <i class="fas fa-lock"></i> Senha *
                            </label>
                            <input type="password" class="form-control" id="senha" name="senha"
                                   placeholder="Mínimo 6 caracteres" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirmar_senha" class="form-label">
                                <i class="fas fa-lock"></i> Confirmar Senha *
                            </label>
                            <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha"
                                   placeholder="Digite a senha novamente" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termos" required>
                            <label class="form-check-label" for="termos">
                                Concordo com os <a href="#" class="text-primary">Termos de Uso</a> e
                                <a href="#" class="text-primary">Política de Privacidade</a>
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus"></i> Criar Conta
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">Já tem uma conta?
                        <a href="login.php" class="text-primary">Faça login aqui</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>