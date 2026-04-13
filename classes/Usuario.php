<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function login($email, $senha) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = TRUE");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            unset($usuario['senha']); // Remove senha do array
            return $usuario;
        }
        return false;
    }

    public function cadastrar($dados) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha, telefone, tipo) VALUES (?, ?, ?, ?, ?)");
        $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $dados['nome'],
            $dados['email'],
            $senha_hash,
            $dados['telefone'] ?? null,
            $dados['tipo'] ?? 'cliente'
        ]);
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT id, nome, email, telefone, tipo, ativo, criado_em FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function listarFuncionarios() {
        $stmt = $this->db->prepare("SELECT id, nome, email, telefone, tipo, ativo FROM usuarios WHERE tipo IN ('funcionario', 'admin') ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function atualizar($id, $dados) {
        $campos = [];
        $valores = [];

        if (isset($dados['nome'])) {
            $campos[] = "nome = ?";
            $valores[] = $dados['nome'];
        }
        if (isset($dados['email'])) {
            $campos[] = "email = ?";
            $valores[] = $dados['email'];
        }
        if (isset($dados['telefone'])) {
            $campos[] = "telefone = ?";
            $valores[] = $dados['telefone'];
        }
        if (isset($dados['ativo'])) {
            $campos[] = "ativo = ?";
            $valores[] = $dados['ativo'];
        }

        if (empty($campos)) return false;

        $valores[] = $id;
        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($valores);
    }
}
?>