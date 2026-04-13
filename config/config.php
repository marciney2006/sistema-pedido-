<?php
/**
 * Configurações Gerais do Sistema
 */

// Configurações do sistema
define('SITE_NAME', 'Lanches Express');
define('SITE_URL', 'http://localhost/sistema-lanches');
define('ADMIN_EMAIL', 'admin@sistema.com');

// Configurações de upload
define('UPLOAD_PATH', __DIR__ . '/../assets/images/produtos/');
define('UPLOAD_URL', SITE_URL . '/assets/images/produtos/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de moeda
define('CURRENCY_SYMBOL', 'R$');
define('CURRENCY_DECIMALS', 2);
define('CURRENCY_SEPARATOR', ',');
define('CURRENCY_THOUSAND', '.');

// Status dos pedidos
define('STATUS_PEDIDOS', [
    'pendente' => 'Pendente',
    'preparando' => 'Preparando',
    'pronto' => 'Pronto',
    'entregue' => 'Entregue',
    'cancelado' => 'Cancelado'
]);

// Tipos de usuário
define('TIPOS_USUARIO', [
    'cliente' => 'Cliente',
    'funcionario' => 'Funcionário',
    'admin' => 'Administrador'
]);

// Configurações de paginação
define('ITENS_POR_PAGINA', 12);

// Configurações de sessão
define('SESSION_LIFETIME', 86400); // 24 horas em segundos

// Configurações de email (para futuras implementações)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls'); // tls ou ssl

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Funções auxiliares
function formatCurrency($value) {
    return CURRENCY_SYMBOL . ' ' . number_format($value, CURRENCY_DECIMALS, CURRENCY_SEPARATOR, CURRENCY_THOUSAND);
}

function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

function getStatusColor($status) {
    $colors = [
        'pendente' => 'warning',
        'preparando' => 'info',
        'pronto' => 'success',
        'entregue' => 'success',
        'cancelado' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function sanitizeString($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Função para verificar se usuário está logado
function isLoggedIn() {
    return isset($_SESSION['usuario']);
}

// Função para verificar tipo de usuário
function hasRole($role) {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === $role;
}

// Função para redirecionar se não estiver logado
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

// Função para redirecionar se não tiver permissão
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header("Location: ../index.php");
        exit();
    }
}
?>