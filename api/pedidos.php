<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Pedido.php';
require_once __DIR__ . '/auth_helper.php';

// Validar token e obter usuario_id
$usuario_id = validarToken();
if ($usuario_id === false) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado. Faça login novamente.']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$pedido = new Pedido();

try {
    switch ($method) {
        case 'GET':
            $rows = $pedido->listarPorUsuario($usuario_id);
            $pedidos = array_map(function($p) {
                return [
                    'id'               => (int)   $p['id'],
                    'usuario_id'       => (int)   $p['usuario_id'],
                    'status'           => $p['status'],
                    'valor_total'      => (float) $p['valor_total'],
                    'tipo_entrega'     => $p['tipo_entrega'],
                    'endereco_entrega' => $p['endereco_entrega'],
                    'observacoes'      => $p['observacoes'],
                    'total_itens'      => (int)   $p['total_itens'],
                    'criado_em'        => $p['criado_em'],
                ];
            }, $rows ?: []);
            echo json_encode(['success' => true, 'pedidos' => $pedidos]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['itens']) || empty($data['itens'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Itens do pedido são obrigatórios']);
                exit();
            }

            $pedido_id = $pedido->criar(
                $usuario_id,
                $data['itens'],
                [
                    'tipo_entrega'     => $data['tipo_entrega'] ?? 'retirada',
                    'endereco_entrega' => $data['endereco_entrega'] ?? null,
                    'observacoes'      => $data['observacoes'] ?? null
                ]
            );

            echo json_encode([
                'success'   => true,
                'pedido_id' => (int) $pedido_id,
                'message'   => 'Pedido criado com sucesso!'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
