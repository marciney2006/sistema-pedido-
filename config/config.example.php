<?php
/**
 * CONFIGURAÇÕES DO SISTEMA - PERSONALIZE AQUI
 *
 * Este arquivo contém todas as configurações que você pode personalizar
 * Copie as configurações que deseja alterar para o arquivo config/config.php
 */

// ============ CONFIGURAÇÕES GERAIS ============

// Nome do sistema
define('SITE_NAME', 'Lanches Express'); // Altere para o nome do seu negócio

// URLs
define('SITE_URL', 'http://localhost/sistema-lanches'); // URL do seu site
define('ADMIN_EMAIL', 'admin@sistema.com'); // Email do administrador

// ============ CONFIGURAÇÕES DE MOEDA ============

define('CURRENCY_SYMBOL', 'R$'); // Símbolo da moeda
define('CURRENCY_DECIMALS', 2); // Casas decimais
define('CURRENCY_SEPARATOR', ','); // Separador decimal
define('CURRENCY_THOUSAND', '.'); // Separador de milhares

// ============ CONFIGURAÇÕES DE UPLOAD ============

define('UPLOAD_PATH', __DIR__ . '/../assets/images/produtos/'); // Pasta para uploads
define('UPLOAD_URL', SITE_URL . '/assets/images/produtos/'); // URL para acessar imagens
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB - tamanho máximo dos arquivos
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']); // Extensões permitidas

// ============ CONFIGURAÇÕES DE PAGINAÇÃO ============

define('ITENS_POR_PAGINA', 12); // Produtos por página no catálogo

// ============ CONFIGURAÇÕES DE SESSÃO ============

define('SESSION_LIFETIME', 86400); // 24 horas em segundos

// ============ CONFIGURAÇÕES DE EMAIL ============
// (Para implementações futuras com envio de email)

define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'

// ============ CONFIGURAÇÕES DE HORÁRIO ============

// Define o fuso horário (importante para datas)
date_default_timezone_set('America/Sao_Paulo');

// Outras opções comuns:
// 'America/Sao_Paulo' - São Paulo
// 'America/Rio_Branco' - Acre
// 'America/Manaus' - Amazonas
// 'America/Fortaleza' - Ceará
// 'America/Recife' - Pernambuco
// 'America/Bahia' - Bahia
// 'America/Noronha' - Fernando de Noronha

// ============ CONFIGURAÇÕES DE TEMPO ============

// Tempo limite para execução de scripts (em segundos)
set_time_limit(300); // 5 minutos

// ============ CONFIGURAÇÕES DE ERROS ============
// (Para desenvolvimento - desative em produção)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ============ CONFIGURAÇÕES DE SEGURANÇA ============

// Força HTTPS (descomente se usar SSL)
// if ($_SERVER['HTTPS'] !== 'on') {
//     header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//     exit();
// }

// ============ CONFIGURAÇÕES DE PERFORMANCE ============

// Compressão GZIP (se suportado pelo servidor)
// ini_set('zlib.output_compression', 'On');
// ini_set('zlib.output_compression_level', '6');

// ============ EXEMPLOS DE PERSONALIZAÇÃO ============

/*
 * EXEMPLOS DE COMO PERSONALIZAR:
 *
 * 1. Alterar nome do negócio:
 * define('SITE_NAME', 'Minha Lanchonete');
 *
 * 2. Alterar moeda para dólar:
 * define('CURRENCY_SYMBOL', 'US$');
 *
 * 3. Aumentar limite de upload:
 * define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
 *
 * 4. Mais produtos por página:
 * define('ITENS_POR_PAGINA', 20);
 *
 * 5. Fuso horário diferente:
 * date_default_timezone_set('America/New_York');
 *
 * 6. Pasta de upload personalizada:
 * define('UPLOAD_PATH', __DIR__ . '/../uploads/produtos/');
 * define('UPLOAD_URL', SITE_URL . '/uploads/produtos/');
 */

// ============ INSTRUÇÕES ============

/*
 * COMO USAR ESTE ARQUIVO:
 *
 * 1. Copie as configurações que deseja alterar
 * 2. Cole no arquivo config/config.php
 * 3. Ajuste os valores conforme necessário
 * 4. Salve e teste o sistema
 *
 * NÃO EDITE ESTE ARQUIVO DIRETAMENTE!
 * Suas alterações serão perdidas em atualizações.
 */