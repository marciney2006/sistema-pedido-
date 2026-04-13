<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Usuario.php';
require_once __DIR__ . '/auth_helper.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['nome']) || empty($input['email']) || empty($input['senha'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nome, email e senha são obrigatórios']);
    exit();
}

$nome     = trim($input['nome']);
$email    = trim($input['email']);
$senha    = $input['senha'];
$telefone = isset($input['telefone']) ? trim($input['telefone']) : null;

if (strlen($nome) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nome deve ter pelo menos 2 caracteres']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit();
}

if (strlen($senha) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Senha deve ter pelo menos 6 caracteres']);
    exit();
}

try {
    $usuario = new Usuario();
    $dados = [
        'nome'     => $nome,
        'email'    => $email,
        'senha'    => $senha,
        'telefone' => $telefone,
        'tipo'     => 'cliente'
    ];

    if ($usuario->cadastrar($dados)) {
        // Buscar o usuário recém-criado para retornar os dados
        $db = getDB();
        $stmt = $db->prepare("SELECT id, nome, email, telefone, tipo, ativo, criado_em FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $novoUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $token = gerarToken((int) $novoUsuario['id'], $novoUsuario['email']);

        echo json_encode([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso',
            'token'   => $token,
            'usuario' => [
                'id'        => (int) $novoUsuario['id'],
                'nome'      => $novoUsuario['nome'],
                'email'     => $novoUsuario['email'],
                'telefone'  => $novoUsuario['telefone'] ?? null,
                'tipo'      => $novoUsuario['tipo'],
                'ativo'     => (bool) $novoUsuario['ativo'],
                'criado_em' => $novoUsuario['criado_em'] ?? ''
            ]
        ]);
    } else {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email já cadastrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
