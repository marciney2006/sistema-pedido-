<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Categoria.php';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

header('Content-Type: application/json');

$categoria = new Categoria();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Buscar categoria específica
                $categoria_data = $categoria->buscarPorId($_GET['id']);
                if ($categoria_data) {
                    echo json_encode(['success' => true, 'categoria' => $categoria_data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Categoria não encontrada']);
                }
            } else {
                // Listar todas as categorias
                $categorias = $categoria->listarTodas();
                echo json_encode(['success' => true, 'categorias' => $categorias]);
            }
            break;

        case 'POST':
            // Criar nova categoria
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (strlen($nome) < 2) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome deve ter pelo menos 2 caracteres']);
                break;
            }

            $resultado = $categoria->criar([
                'nome' => $nome,
                'descricao' => $descricao
            ]);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Categoria criada com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao criar categoria']);
            }
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID da categoria é obrigatório']);
                break;
            }

            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (strlen($nome) < 2) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome deve ter pelo menos 2 caracteres']);
                break;
            }

            $resultado = $categoria->atualizar($_GET['id'], [
                'nome' => $nome,
                'descricao' => $descricao
            ]);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Categoria atualizada com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar categoria']);
            }
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID da categoria é obrigatório']);
                break;
            }

            // Verificar se a categoria tem produtos
            $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) as total FROM produtos WHERE categoria_id = ?");
            $stmt->execute([$_GET['id']]);
            $total_produtos = $stmt->fetch()['total'];

            if ($total_produtos > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Não é possível deletar uma categoria que possui produtos']);
                break;
            }

            $resultado = $categoria->deletar($_GET['id']);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Categoria deletada com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar categoria']);
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