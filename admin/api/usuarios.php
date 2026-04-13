<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Usuario.php';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

header('Content-Type: application/json');

$usuario = new Usuario();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Buscar usuário específico
                $usuario_data = $usuario->buscarPorId($_GET['id']);
                if ($usuario_data) {
                    // Não retornar senha
                    unset($usuario_data['senha']);
                    echo json_encode(['success' => true, 'usuario' => $usuario_data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
                }
            } else {
                // Listar todos os usuários
                $usuarios = $usuario->listarTodos();
                // Não retornar senhas
                $usuarios = array_map(function($u) {
                    unset($u['senha']);
                    return $u;
                }, $usuarios);
                echo json_encode(['success' => true, 'usuarios' => $usuarios]);
            }
            break;

        case 'POST':
            // Criar novo usuário
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $tipo = $_POST['tipo'] ?? '';
            $ativo = isset($_POST['ativo']) ? 1 : 0;

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email inválido']);
                break;
            }

            if (empty($senha) || strlen($senha) < 6) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Senha deve ter pelo menos 6 caracteres']);
                break;
            }

            $tipos_validos = ['cliente', 'funcionario', 'admin'];
            if (empty($tipo) || !in_array($tipo, $tipos_validos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tipo de usuário inválido']);
                break;
            }

            // Verificar se email já existe
            $stmt = $GLOBALS['pdo']->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email já cadastrado']);
                break;
            }

            $resultado = $usuario->criar([
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'tipo' => $tipo,
                'ativo' => $ativo
            ]);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuário criado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao criar usuário']);
            }
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do usuário é obrigatório']);
                break;
            }

            // Buscar usuário atual
            $usuario_atual = $usuario->buscarPorId($_GET['id']);
            if (!$usuario_atual) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
                break;
            }

            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $tipo = $_POST['tipo'] ?? '';
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            $senha = $_POST['senha'] ?? '';

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email inválido']);
                break;
            }

            $tipos_validos = ['cliente', 'funcionario', 'admin'];
            if (empty($tipo) || !in_array($tipo, $tipos_validos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tipo de usuário inválido']);
                break;
            }

            // Verificar se email já existe (exceto para o próprio usuário)
            $stmt = $GLOBALS['pdo']->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_GET['id']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email já cadastrado por outro usuário']);
                break;
            }

            $dados_atualizar = [
                'nome' => $nome,
                'email' => $email,
                'tipo' => $tipo,
                'ativo' => $ativo
            ];

            // Se senha foi fornecida, incluir na atualização
            if (!empty($senha)) {
                if (strlen($senha) < 6) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Nova senha deve ter pelo menos 6 caracteres']);
                    break;
                }
                $dados_atualizar['senha'] = $senha;
            }

            $resultado = $usuario->atualizar($_GET['id'], $dados_atualizar);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar usuário']);
            }
            break;

        case 'PATCH':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do usuário é obrigatório']);
                break;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['ativo'])) {
                $resultado = $usuario->toggleStatus($_GET['id'], $input['ativo']);
                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Campo para atualizar não especificado']);
            }
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do usuário é obrigatório']);
                break;
            }

            // Não permitir deletar o próprio usuário admin
            if ($_GET['id'] == $_SESSION['usuario']['id']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Não é possível deletar o próprio usuário']);
                break;
            }

            // Verificar se o usuário tem pedidos (se for cliente)
            $stmt = $GLOBALS['pdo']->prepare("SELECT tipo FROM usuarios WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $tipo_usuario = $stmt->fetch()['tipo'];

            if ($tipo_usuario === 'cliente') {
                $stmt = $GLOBALS['pdo']->prepare("SELECT COUNT(*) as total FROM pedidos WHERE usuario_id = ?");
                $stmt->execute([$_GET['id']]);
                $total_pedidos = $stmt->fetch()['total'];

                if ($total_pedidos > 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Não é possível deletar um cliente que possui pedidos']);
                    break;
                }
            }

            $resultado = $usuario->deletar($_GET['id']);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Usuário deletado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar usuário']);
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