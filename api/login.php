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

if (!$input || empty($input['email']) || empty($input['senha'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios']);
    exit();
}

$email = trim($input['email']);
$senha = $input['senha'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit();
}

try {
    $usuario = new Usuario();
    $dados = $usuario->login($email, $senha);

    if ($dados) {
        $token = gerarToken((int) $dados['id'], $dados['email']);
        echo json_encode([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'token'   => $token,
            'usuario' => [
                'id'        => (int) $dados['id'],
                'nome'      => $dados['nome'],
                'email'     => $dados['email'],
                'telefone'  => $dados['telefone'] ?? null,
                'tipo'      => $dados['tipo'],
                'ativo'     => (bool) $dados['ativo'],
                'criado_em' => $dados['criado_em'] ?? ''
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
