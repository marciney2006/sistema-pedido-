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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = new Usuario();
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $dados_usuario = $usuario->login($email, $senha);

    if ($dados_usuario) {
        $_SESSION['usuario'] = $dados_usuario;
        header("Location: index.php");
        exit();
    } else {
        $erro = "Email ou senha incorretos!";
    }
}

$titulo = 'Login - Lanches Express';
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🍔</div>
                    <h2 class="card-title">Bem-vindo de volta!</h2>
                    <p class="text-muted">Entre com suas credenciais para continuar</p>
                </div>

                <?php if ($erro): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $erro ?>
                    </div>
                <?php endif; ?>

                <form method="POST" data-validate="true">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="seu@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <input type="password" class="form-control" id="senha" name="senha"
                               placeholder="Digite sua senha" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Entrar
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">Não tem uma conta?
                        <a href="cadastro.php" class="text-primary">Cadastre-se aqui</a>
                    </p>
                </div>

                <hr class="my-4">

                <div class="text-center">
                    <small class="text-muted">
                        <strong>Conta de Teste:</strong><br>
                        Email: admin@sistema.com<br>
                        Senha: password
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>