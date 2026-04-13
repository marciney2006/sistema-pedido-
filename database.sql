-- Sistema de Pedidos de Lanches - Script de Criação do Banco de Dados
-- Execute este script no MySQL para criar todas as tabelas necessárias

CREATE DATABASE IF NOT EXISTS sistema_lanches CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_lanches;

-- Tabela de usuários (clientes e funcionários)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    tipo ENUM('cliente', 'funcionario', 'admin') DEFAULT 'cliente',
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de categorias de produtos
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de produtos (lanches, bebidas, etc.)
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    categoria_id INT,
    imagem VARCHAR(255),
    disponivel BOOLEAN DEFAULT TRUE,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabela de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    status ENUM('pendente', 'preparando', 'pronto', 'entregue', 'cancelado') DEFAULT 'pendente',
    tipo_entrega ENUM('retirada', 'entrega') DEFAULT 'retirada',
    endereco_entrega TEXT,
    observacoes TEXT,
    valor_total DECIMAL(10,2) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de itens do pedido
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Inserir dados iniciais

-- Usuários
INSERT INTO usuarios (nome, email, senha, telefone, tipo) VALUES
('Administrador', 'admin@sistema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11999999999', 'admin'), -- senha: password
('João Cliente', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11888888888', 'cliente'), -- senha: password
('Maria Funcionária', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '11777777777', 'funcionario'); -- senha: password

-- Categorias
INSERT INTO categorias (nome, descricao) VALUES
('Lanches', 'Hambúrgueres, sanduíches e opções rápidas'),
('Bebidas', 'Refrigerantes, sucos e bebidas diversas'),
('Sobremesas', 'Doces, sorvetes e sobremesas'),
('Porções', 'Batatas fritas, onion rings e acompanhamentos');

-- Produtos
INSERT INTO produtos (nome, descricao, preco, categoria_id) VALUES
-- Lanches
('X-Burguer', 'Hambúrguer com queijo, alface, tomate e molho especial', 15.90, 1),
('X-Bacon', 'Hambúrguer com bacon crocante, queijo e molho', 18.90, 1),
('Hot Dog Especial', 'Cachorro-quente com salsicha, queijo, batata palha e molhos', 12.90, 1),
('Sanduíche Natural', 'Peito de peru, queijo branco, alface, tomate e maionese', 14.50, 1),

-- Bebidas
('Coca-Cola 350ml', 'Refrigerante Coca-Cola lata 350ml', 5.50, 2),
('Suco Natural de Laranja', 'Suco de laranja natural 300ml', 7.90, 2),
('Água Mineral', 'Água mineral sem gás 500ml', 3.50, 2),
('Milk Shake', 'Milk shake de chocolate ou baunilha 400ml', 9.90, 2),

-- Sobremesas
('Sorvete Sundae', 'Sorvete com calda de chocolate e chantilly', 8.90, 3),
('Torta de Maçã', 'Fatia de torta de maçã com canela', 6.50, 3),
('Pudim', 'Pudim de leite condensado caseiro', 5.90, 3),

-- Porções
('Batata Frita Grande', 'Porção grande de batata frita crocante', 12.90, 4),
('Onion Rings', 'Anéis de cebola empanados e fritos', 10.50, 4),
('Nuggets (10 unidades)', 'Nuggets de frango empanados', 11.90, 4);

-- Índices para melhor performance
CREATE INDEX idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX idx_pedidos_status ON pedidos(status);
CREATE INDEX idx_itens_pedido ON itens_pedido(pedido_id);
CREATE INDEX idx_produtos_categoria ON produtos(categoria_id);