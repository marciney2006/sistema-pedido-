<?php
/**
 * Script de Instalação - Sistema de Pedidos de Lanches
 *
 * Este script verifica os pré-requisitos e ajuda na configuração inicial
 */

// Configurações de erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 Instalação - Sistema de Pedidos de Lanches</h1>";

// Verificar versão do PHP
echo "<h3>📋 Verificando Pré-requisitos...</h3>";
echo "<ul>";

$php_version = phpversion();
$php_ok = version_compare($php_version, '8.0.0', '>=');
echo "<li>PHP Version: <strong>$php_version</strong> - " . ($php_ok ? '<span style="color:green">✅ OK</span>' : '<span style="color:red">❌ Precisa PHP 8.0+</span>') . "</li>";

// Verificar extensões necessárias
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'session', 'json'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo "<li>Extensão $ext: " . ($loaded ? '<span style="color:green">✅ Carregada</span>' : '<span style="color:red">❌ Não carregada</span>') . "</li>";
}

echo "</ul>";

// Verificar arquivos necessários
echo "<h3>📁 Verificando Arquivos...</h3>";
echo "<ul>";

$files_to_check = [
    'config/database.php' => 'Configuração do banco de dados',
    'classes/Usuario.php' => 'Classe Usuario',
    'classes/Produto.php' => 'Classe Produto',
    'classes/Pedido.php' => 'Classe Pedido',
    'classes/Categoria.php' => 'Classe Categoria',
    'database.sql' => 'Script do banco de dados',
    'templates/header.php' => 'Template header',
    'templates/footer.php' => 'Template footer'
];

foreach ($files_to_check as $file => $description) {
    $exists = file_exists($file);
    echo "<li>$description: " . ($exists ? '<span style="color:green">✅ Existe</span>' : '<span style="color:red">❌ Não encontrado</span>') . "</li>";
}

echo "</ul>";

// Verificar permissões de diretório
echo "<h3>🔐 Verificando Permissões...</h3>";
echo "<ul>";

$dirs_to_check = [
    'assets/images/produtos/' => 'Upload de imagens de produtos'
];

foreach ($dirs_to_check as $dir => $description) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "<li>$description: <span style=\"color:blue\">📁 Diretório criado</span></li>";
    } else {
        $writable = is_writable($dir);
        echo "<li>$description: " . ($writable ? '<span style="color:green">✅ Gravável</span>' : '<span style="color:red">❌ Sem permissão de escrita</span>') . "</li>";
    }
}

echo "</ul>";

// Testar conexão com banco de dados
echo "<h3>🗄️ Testando Conexão com Banco...</h3>";

try {
    require_once 'config/database.php';
    $db = getDB();
    $stmt = $db->query("SELECT 1");
    echo "<span style=\"color:green\">✅ Conexão com banco de dados estabelecida com sucesso!</span><br>";
} catch (Exception $e) {
    echo "<span style=\"color:red\">❌ Erro na conexão com banco de dados: " . $e->getMessage() . "</span><br>";
    echo "<strong>Soluções possíveis:</strong><br>";
    echo "- Verifique se o MySQL está rodando<br>";
    echo "- Confirme as credenciais no arquivo config/database.php<br>";
    echo "- Certifique-se de que o banco de dados 'sistema_lanches' existe<br>";
}

// Próximos passos
echo "<h3>🎯 Próximos Passos</h3>";
echo "<ol>";
echo "<li><strong>Execute o script do banco:</strong> Importe o arquivo <code>database.sql</code> no seu MySQL</li>";
echo "<li><strong>Configure o banco:</strong> Ajuste as credenciais em <code>config/database.php</code> se necessário</li>";
echo "<li><strong>Acesse o sistema:</strong> <a href=\"index.php\" target=\"_blank\">Ir para a página inicial</a></li>";
echo "<li><strong>Login admin:</strong> Use admin@sistema.com / admin123</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>📖 Documentação completa:</strong> Veja o arquivo <code>README.md</code></p>";
echo "<p><strong>🆘 Suporte:</strong> Abra uma issue no repositório do projeto</p>";
?>