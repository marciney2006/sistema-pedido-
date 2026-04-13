<?php
require_once __DIR__ . '/../config/database.php';

class Produto {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listarPorCategoria($categoria_id = null) {
        if ($categoria_id) {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nome as categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.categoria_id = ? AND p.ativo = TRUE AND p.disponivel = TRUE
                ORDER BY p.nome
            ");
            $stmt->execute([$categoria_id]);
        } else {
            $stmt = $this->db->prepare("
                SELECT p.*, c.nome as categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.ativo = TRUE AND p.disponivel = TRUE
                ORDER BY c.nome, p.nome
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nome as categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = ? AND p.ativo = TRUE
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function listarTodos() {
        $stmt = $this->db->prepare("
            SELECT p.*, c.nome as categoria_nome
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.ativo DESC, c.nome, p.nome
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function salvar($dados) {
        if (isset($dados['id']) && $dados['id']) {
            // Atualizar
            $stmt = $this->db->prepare("
                UPDATE produtos SET
                nome = ?, descricao = ?, preco = ?, categoria_id = ?,
                imagem = ?, disponivel = ?, ativo = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['preco'],
                $dados['categoria_id'],
                $dados['imagem'] ?? null,
                $dados['disponivel'] ?? true,
                $dados['ativo'] ?? true,
                $dados['id']
            ]);
        } else {
            // Inserir
            $stmt = $this->db->prepare("
                INSERT INTO produtos (nome, descricao, preco, categoria_id, imagem, disponivel, ativo)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $dados['nome'],
                $dados['descricao'] ?? null,
                $dados['preco'],
                $dados['categoria_id'],
                $dados['imagem'] ?? null,
                $dados['disponivel'] ?? true,
                $dados['ativo'] ?? true
            ]);
        }
    }

    public function excluir($id) {
        $stmt = $this->db->prepare("UPDATE produtos SET ativo = FALSE WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleDisponibilidade($id) {
        $stmt = $this->db->prepare("UPDATE produtos SET disponivel = NOT disponivel WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>