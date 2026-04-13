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
require_once __DIR__ . '/../classes/Categoria.php';

$categoria = new Categoria();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit();
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $categoriaData = $categoria->buscarPorId((int) $_GET['id']);

        if (!$categoriaData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Categoria não encontrada']);
            exit();
        }

        echo json_encode(['success' => true, 'categoria' => $categoriaData]);
        exit();
    }

    $categorias = $categoria->listarAtivas();
    $categorias = array_map(function($c) {
        return [
            'id'        => (int)  $c['id'],
            'nome'      => $c['nome'],
            'descricao' => $c['descricao'],
            'ativo'     => (bool) $c['ativo'],
            'criado_em' => $c['criado_em'],
        ];
    }, $categorias ?: []);
    echo json_encode(['success' => true, 'categorias' => $categorias]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
