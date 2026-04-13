<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Pedido.php';

// Verificar se está logado como admin/funcionario
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['tipo'], ['admin', 'funcionario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

header('Content-Type: application/json');

$pedido = new Pedido();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Buscar pedido específico
                $pedido_data = $pedido->buscarPorId($_GET['id']);
                if ($pedido_data) {
                    echo json_encode(['success' => true, 'pedido' => $pedido_data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
                }
            } else {
                // Listar pedidos com filtros
                $status = isset($_GET['status']) ? $_GET['status'] : 'todos';
                $pedidos = $pedido->listarTodos($status);
                echo json_encode(['success' => true, 'pedidos' => $pedidos]);
            }
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do pedido é obrigatório']);
                break;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['status'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status é obrigatório']);
                break;
            }

            $status_validos = ['pendente', 'preparando', 'pronto', 'entregue', 'cancelado'];
            if (!in_array($input['status'], $status_validos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status inválido']);
                break;
            }

            $resultado = $pedido->atualizarStatus($_GET['id'], $input['status']);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
            }
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do pedido é obrigatório']);
                break;
            }

            // Verificar se o pedido pode ser deletado (apenas se for cancelado)
            $pedido_data = $pedido->buscarPorId($_GET['id']);
            if (!$pedido_data) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
                break;
            }

            if ($pedido_data['status'] !== 'cancelado') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Apenas pedidos cancelados podem ser deletados']);
                break;
            }

            $resultado = $pedido->deletar($_GET['id']);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Pedido deletado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar pedido']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?>