<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Produto.php';

// Verificar se está logado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

header('Content-Type: application/json');

$produto = new Produto();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Buscar produto específico
                $produto_data = $produto->buscarPorId($_GET['id']);
                if ($produto_data) {
                    echo json_encode(['success' => true, 'produto' => $produto_data]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
                }
            } else {
                // Listar todos os produtos
                $produtos = $produto->listarTodos();
                echo json_encode(['success' => true, 'produtos' => $produtos]);
            }
            break;

        case 'POST':
            // Criar novo produto
            $nome = trim($_POST['nome'] ?? '');
            $categoria_id = $_POST['categoria_id'] ?? '';
            $preco = $_POST['preco'] ?? '';
            $descricao = trim($_POST['descricao'] ?? '');
            $ativo = isset($_POST['ativo']) ? 1 : 0;

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (empty($categoria_id) || !is_numeric($categoria_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Categoria inválida']);
                break;
            }

            if (!is_numeric($preco) || $preco < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Preço inválido']);
                break;
            }

            // Processar upload de imagem
            $imagem_nome = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../assets/images/produtos/';

                // Criar diretório se não existir
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($extensao, $extensoes_permitidas)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
                    break;
                }

                if ($_FILES['imagem']['size'] > 2 * 1024 * 1024) { // 2MB
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande (máx. 2MB)']);
                    break;
                }

                $imagem_nome = uniqid() . '.' . $extensao;
                $caminho_completo = $upload_dir . $imagem_nome;

                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem']);
                    break;
                }
            }

            $resultado = $produto->criar([
                'nome' => $nome,
                'categoria_id' => $categoria_id,
                'preco' => $preco,
                'descricao' => $descricao,
                'imagem' => $imagem_nome,
                'ativo' => $ativo
            ]);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Produto criado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao criar produto']);
            }
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                break;
            }

            // Buscar produto atual
            $produto_atual = $produto->buscarPorId($_GET['id']);
            if (!$produto_atual) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
                break;
            }

            $nome = trim($_POST['nome'] ?? '');
            $categoria_id = $_POST['categoria_id'] ?? '';
            $preco = $_POST['preco'] ?? '';
            $descricao = trim($_POST['descricao'] ?? '');
            $ativo = isset($_POST['ativo']) ? 1 : 0;

            // Validações
            if (empty($nome)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                break;
            }

            if (empty($categoria_id) || !is_numeric($categoria_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Categoria inválida']);
                break;
            }

            if (!is_numeric($preco) || $preco < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Preço inválido']);
                break;
            }

            // Processar upload de imagem
            $imagem_nome = $produto_atual['imagem'];
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../assets/images/produtos/';

                // Criar diretório se não existir
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($extensao, $extensoes_permitidas)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
                    break;
                }

                if ($_FILES['imagem']['size'] > 2 * 1024 * 1024) { // 2MB
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande (máx. 2MB)']);
                    break;
                }

                // Deletar imagem antiga se existir
                if ($produto_atual['imagem']) {
                    $caminho_antigo = $upload_dir . $produto_atual['imagem'];
                    if (file_exists($caminho_antigo)) {
                        unlink($caminho_antigo);
                    }
                }

                $imagem_nome = uniqid() . '.' . $extensao;
                $caminho_completo = $upload_dir . $imagem_nome;

                if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_completo)) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem']);
                    break;
                }
            }

            $resultado = $produto->atualizar($_GET['id'], [
                'nome' => $nome,
                'categoria_id' => $categoria_id,
                'preco' => $preco,
                'descricao' => $descricao,
                'imagem' => $imagem_nome,
                'ativo' => $ativo
            ]);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Produto atualizado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar produto']);
            }
            break;

        case 'PATCH':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                break;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['ativo'])) {
                $resultado = $produto->toggleStatus($_GET['id'], $input['ativo']);
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
                echo json_encode(['success' => false, 'message' => 'ID do produto é obrigatório']);
                break;
            }

            // Buscar produto para deletar imagem
            $produto_data = $produto->buscarPorId($_GET['id']);
            if ($produto_data && $produto_data['imagem']) {
                $upload_dir = '../../assets/images/produtos/';
                $caminho_imagem = $upload_dir . $produto_data['imagem'];
                if (file_exists($caminho_imagem)) {
                    unlink($caminho_imagem);
                }
            }

            $resultado = $produto->deletar($_GET['id']);

            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Produto deletado com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro ao deletar produto']);
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