<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../../config/database.php';
require_once '../../classes/Pedido.php';

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$pedido = new Pedido();

try {
    switch ($method) {
        case 'GET':
            // Buscar pedidos do usuário
            $pedidos = $pedido->listarPorUsuario($_SESSION['usuario']['id']);
            echo json_encode(['success' => true, 'pedidos' => $pedidos]);
            break;

        case 'POST':
            // Criar novo pedido
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['itens']) || empty($data['itens'])) {
                throw new Exception('Dados do pedido inválidos');
            }

            $pedido_id = $pedido->criar(
                $_SESSION['usuario']['id'],
                $data['itens'],
                [
                    'tipo_entrega' => $data['tipo_entrega'] ?? 'retirada',
                    'endereco_entrega' => $data['endereco_entrega'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null
                ]
            );

            echo json_encode([
                'success' => true,
                'pedido_id' => $pedido_id,
                'message' => 'Pedido criado com sucesso!'
            ]);
            break;

        case 'PUT':
            // Atualizar status (apenas para admin/funcionários)
            if ($_SESSION['usuario']['tipo'] === 'cliente') {
                http_response_code(403);
                echo json_encode(['error' => 'Acesso negado']);
                exit();
            }

            $pedido_id = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$pedido_id || !isset($data['status'])) {
                throw new Exception('Dados inválidos');
            }

            $resultado = $pedido->atualizarStatus($pedido_id, $data['status']);
            echo json_encode(['success' => $resultado]);
            break;

        case 'DELETE':
            // Cancelar pedido
            $pedido_id = $_GET['id'] ?? null;

            if (!$pedido_id) {
                throw new Exception('ID do pedido não informado');
            }

            $resultado = $pedido->cancelar($pedido_id, $_SESSION['usuario']['id']);
            echo json_encode(['success' => $resultado]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            break;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>