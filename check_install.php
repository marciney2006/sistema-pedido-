<?php
/**
 * Script de Verificação de Instalação
 * Verifica se o sistema está configurado corretamente
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$titulo = 'Verificação de Instalação - ' . SITE_NAME;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h4 mb-0">
                            <i class="fas fa-check-circle"></i> Verificação de Instalação
                        </h1>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Este script verifica se todas as configurações necessárias estão funcionando corretamente.
                        </p>

                        <?php
                        $checks = [];
                        $all_passed = true;

                        // Verificar PHP
                        $checks[] = [
                            'name' => 'Versão do PHP',
                            'status' => version_compare(PHP_VERSION, '8.0.0', '>='),
                            'message' => 'PHP ' . PHP_VERSION . ' - ' . (version_compare(PHP_VERSION, '8.0.0', '>=') ? 'OK' : 'Recomendado: 8.0+'),
                            'required' => true
                        ];

                        // Verificar extensões PHP
                        $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
                        foreach ($extensions as $ext) {
                            $has_ext = extension_loaded($ext);
                            $checks[] = [
                                'name' => 'Extensão PHP: ' . $ext,
                                'status' => $has_ext,
                                'message' => $has_ext ? 'Carregada' : 'Não encontrada',
                                'required' => true
                            ];
                            if (!$has_ext) $all_passed = false;
                        }

                        // Verificar conexão com banco
                        try {
                            $db = getDB();
                            $stmt = $db->query("SELECT 1");
                            $db_ok = true;
                            $db_message = 'Conexão estabelecida';
                        } catch (Exception $e) {
                            $db_ok = false;
                            $db_message = 'Erro: ' . $e->getMessage();
                            $all_passed = false;
                        }

                        $checks[] = [
                            'name' => 'Conexão com Banco de Dados',
                            'status' => $db_ok,
                            'message' => $db_message,
                            'required' => true
                        ];

                        // Verificar tabelas do banco
                        if ($db_ok) {
                            $tables = ['usuarios', 'categorias', 'produtos', 'pedidos', 'itens_pedido'];
                            foreach ($tables as $table) {
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) FROM $table");
                                    $count = $stmt->fetchColumn();
                                    $table_ok = true;
                                    $table_message = "OK - $count registro(s)";
                                } catch (Exception $e) {
                                    $table_ok = false;
                                    $table_message = 'Tabela não encontrada';
                                    $all_passed = false;
                                }

                                $checks[] = [
                                    'name' => 'Tabela: ' . $table,
                                    'status' => $table_ok,
                                    'message' => $table_message,
                                    'required' => true
                                ];
                            }
                        }

                        // Verificar pastas
                        $folders = [
                            'assets/images/produtos' => 'Uploads de produtos',
                            'assets/css' => 'Arquivos CSS',
                            'assets/js' => 'Arquivos JavaScript',
                            'config' => 'Configurações',
                            'classes' => 'Classes PHP',
                            'templates' => 'Templates',
                            'admin' => 'Painel Admin',
                            'cliente' => 'Área do Cliente'
                        ];

                        foreach ($folders as $folder => $description) {
                            $folder_path = __DIR__ . '/' . $folder;
                            $folder_ok = is_dir($folder_path) && is_readable($folder_path);
                            $checks[] = [
                                'name' => 'Pasta: ' . $description,
                                'status' => $folder_ok,
                                'message' => $folder_ok ? 'OK' : 'Pasta não encontrada ou sem permissão',
                                'required' => true
                            ];
                            if (!$folder_ok) $all_passed = false;
                        }

                        // Verificar permissões de escrita
                        $writable_folders = ['assets/images/produtos'];
                        foreach ($writable_folders as $folder) {
                            $folder_path = __DIR__ . '/' . $folder;
                            $writable = is_writable($folder_path);
                            $checks[] = [
                                'name' => 'Permissão de escrita: ' . $folder,
                                'status' => $writable,
                                'message' => $writable ? 'OK' : 'Sem permissão de escrita',
                                'required' => false
                            ];
                        }

                        // Verificar arquivos principais
                        $files = [
                            'index.php' => 'Página inicial',
                            'config/database.php' => 'Configuração do banco',
                            'config/config.php' => 'Configurações gerais',
                            'classes/Usuario.php' => 'Classe Usuario',
                            'classes/Produto.php' => 'Classe Produto',
                            'classes/Pedido.php' => 'Classe Pedido',
                            'templates/header.php' => 'Template header',
                            'templates/footer.php' => 'Template footer'
                        ];

                        foreach ($files as $file => $description) {
                            $file_path = __DIR__ . '/' . $file;
                            $file_ok = file_exists($file_path) && is_readable($file_path);
                            $checks[] = [
                                'name' => 'Arquivo: ' . $description,
                                'status' => $file_ok,
                                'message' => $file_ok ? 'OK' : 'Arquivo não encontrado',
                                'required' => true
                            ];
                            if (!$file_ok) $all_passed = false;
                        }
                        ?>

                        <!-- Resultado geral -->
                        <div class="alert <?php echo $all_passed ? 'alert-success' : 'alert-warning'; ?> mb-4">
                            <h5 class="mb-2">
                                <i class="fas <?php echo $all_passed ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                                <?php echo $all_passed ? 'Instalação OK!' : 'Problemas encontrados'; ?>
                            </h5>
                            <p class="mb-0">
                                <?php if ($all_passed): ?>
                                    Todas as verificações passaram! O sistema está pronto para uso.
                                <?php else: ?>
                                    Alguns itens precisam ser corrigidos. Verifique os detalhes abaixo.
                                <?php endif; ?>
                            </p>
                        </div>

                        <!-- Tabela de verificações -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Verificação</th>
                                        <th>Status</th>
                                        <th>Detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($checks as $check): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($check['name']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $check['status'] ? 'bg-success' : 'bg-danger'; ?>">
                                                    <i class="fas <?php echo $check['status'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                                    <?php echo $check['status'] ? 'OK' : 'Erro'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($check['message']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Ações -->
                        <div class="mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="index.php" class="btn btn-primary w-100">
                                        <i class="fas fa-home"></i> Ir para o Sistema
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="admin/dashboard.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-tachometer-alt"></i> Painel Admin
                                    </a>
                                </div>
                            </div>

                            <?php if (!$all_passed): ?>
                                <div class="alert alert-info mt-3">
                                    <h6><i class="fas fa-info-circle"></i> Precisa de ajuda?</h6>
                                    <p class="mb-2">Consulte o arquivo <code>XAMPP_SETUP.md</code> para instruções detalhadas de instalação.</p>
                                    <a href="XAMPP_SETUP.md" class="btn btn-sm btn-info">
                                        <i class="fas fa-book"></i> Ver Instruções
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>