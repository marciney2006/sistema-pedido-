<?php
require_once __DIR__ . '/../config/database.php';

class Pedido {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function criar($usuario_id, $itens, $dados_pedido = []) {
        try {
            $this->db->beginTransaction();

            // Calcular valor total
            $valor_total = 0;
            foreach ($itens as $item) {
                $produto_id = $item['produto_id'] ?? $item['id'] ?? null;
                $produto = $this->buscarProduto($produto_id);

                if (!$produto) {
                    throw new Exception('Produto inválido ou indisponível');
                }

                $valor_total += $produto['preco'] * $item['quantidade'];
            }

            // Inserir pedido
            $stmt = $this->db->prepare("
                INSERT INTO pedidos (usuario_id, tipo_entrega, endereco_entrega, observacoes, valor_total)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $usuario_id,
                $dados_pedido['tipo_entrega'] ?? 'retirada',
                $dados_pedido['endereco_entrega'] ?? null,
                $dados_pedido['observacoes'] ?? null,
                $valor_total
            ]);

            $pedido_id = $this->db->lastInsertId();

            // Inserir itens do pedido
            $stmt_item = $this->db->prepare("
                INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario, observacoes)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($itens as $item) {
                $produto_id = $item['produto_id'] ?? $item['id'] ?? null;
                $produto = $this->buscarProduto($produto_id);
                $stmt_item->execute([
                    $pedido_id,
                    $produto_id,
                    $item['quantidade'],
                    $produto['preco'],
                    $item['observacoes'] ?? null
                ]);
            }

            $this->db->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function buscarProduto($id) {
        $stmt = $this->db->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = TRUE");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.nome as cliente_nome, u.email as cliente_email, u.telefone as cliente_telefone
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch();

        if ($pedido) {
            $pedido['itens'] = $this->buscarItens($id);
        }

        return $pedido;
    }

    public function buscarItens($pedido_id) {
        $stmt = $this->db->prepare("
            SELECT ip.*, p.nome as produto_nome, p.descricao as produto_descricao
            FROM itens_pedido ip
            JOIN produtos p ON ip.produto_id = p.id
            WHERE ip.pedido_id = ?
        ");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll();
    }

    public function listarPorUsuario($usuario_id) {
        $stmt = $this->db->prepare("
            SELECT p.*, COUNT(ip.id) as total_itens
            FROM pedidos p
            LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
            WHERE p.usuario_id = ?
            GROUP BY p.id
            ORDER BY p.criado_em DESC
        ");
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll();
    }

    public function listarTodos($status = null) {
        $sql = "
            SELECT p.*, u.nome as cliente_nome, COUNT(ip.id) as total_itens
            FROM pedidos p
            JOIN usuarios u ON p.usuario_id = u.id
            LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
        ";

        $params = [];
        if ($status) {
            $sql .= " WHERE p.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY p.id ORDER BY p.criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function atualizarStatus($pedido_id, $status) {
        $stmt = $this->db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $pedido_id]);
    }

    public function cancelar($pedido_id, $usuario_id = null) {
        $sql = "UPDATE pedidos SET status = 'cancelado' WHERE id = ?";
        $params = [$pedido_id];

        if ($usuario_id) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getEstatisticas() {
        $stats = [];

        // Total de pedidos hoje
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM pedidos
            WHERE DATE(criado_em) = CURDATE()
        ");
        $stmt->execute();
        $stats['pedidos_hoje'] = $stmt->fetch()['total'];

        // Valor total hoje
        $stmt = $this->db->prepare("
            SELECT SUM(valor_total) as total
            FROM pedidos
            WHERE DATE(criado_em) = CURDATE() AND status != 'cancelado'
        ");
        $stmt->execute();
        $stats['valor_hoje'] = $stmt->fetch()['total'] ?? 0;

        // Pedidos pendentes
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM pedidos
            WHERE status IN ('pendente', 'preparando')
        ");
        $stmt->execute();
        $stats['pedidos_pendentes'] = $stmt->fetch()['total'];

        return $stats;
    }
}
?>