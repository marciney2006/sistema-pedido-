<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listarAtivas() {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE ativo = TRUE ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listarTodas() {
        $stmt = $this->db->prepare("SELECT * FROM categorias ORDER BY ativo DESC, nome");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function salvar($dados) {
        if (isset($dados['id']) && $dados['id']) {
            // Atualizar
            $stmt = $this->db->prepare("UPDATE categorias SET nome = ?, descricao = ?, ativo = ? WHERE id = ?");
            return $stmt->execute([
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['ativo'] ?? true,
                $dados['id']
            ]);
        } else {
            // Inserir
            $stmt = $this->db->prepare("INSERT INTO categorias (nome, descricao, ativo) VALUES (?, ?, ?)");
            return $stmt->execute([
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['ativo'] ?? true
            ]);
        }
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("UPDATE categorias SET ativo = FALSE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleAtivo($id) {
        $stmt = $this->db->prepare("UPDATE categorias SET ativo = NOT ativo WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>