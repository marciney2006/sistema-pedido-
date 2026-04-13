<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Produto.php';

$produto = new Produto();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit();
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $produtoData = $produto->buscarPorId((int) $_GET['id']);

        if (!$produtoData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
            exit();
        }

        echo json_encode(['success' => true, 'produto' => normalizarProduto($produtoData)]);
        exit();
    }

    if (isset($_GET['categoria_id']) && is_numeric($_GET['categoria_id'])) {
        $produtos = $produto->listarPorCategoria((int) $_GET['categoria_id']);
        echo json_encode(['success' => true, 'produtos' => array_map('normalizarProduto', $produtos ?: [])]);
        exit();
    }

    $produtos = $produto->listarPorCategoria();
    echo json_encode(['success' => true, 'produtos' => array_map('normalizarProduto', $produtos ?: [])]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

function normalizarProduto(array $p): array {
    return [
        'id'             => (int)   $p['id'],
        'nome'           => $p['nome'],
        'descricao'      => $p['descricao'],
        'preco'          => (float) $p['preco'],
        'categoria_id'   => (int)   $p['categoria_id'],
        'imagem'         => $p['imagem'] ?? null,
        'disponivel'     => (bool)  $p['disponivel'],
        'ativo'          => (bool)  $p['ativo'],
        'criado_em'      => $p['criado_em'],
        'categoria_nome' => $p['categoria_nome'] ?? null,
    ];
}
