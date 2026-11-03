CREATE DATABASE IF NOT EXISTS cupcake_gourmet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cupcake_gourmet;
CREATE TABLE categoria (id_categoria INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(80) NOT NULL UNIQUE);
CREATE TABLE produto (
 id_produto INT AUTO_INCREMENT PRIMARY KEY, id_categoria INT NOT NULL, nome VARCHAR(120) NOT NULL,
 descricao TEXT, preco DECIMAL(10,2) NOT NULL, imagem_url VARCHAR(255), ativo BOOLEAN NOT NULL DEFAULT TRUE,
 CHECK (preco >= 0), FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);
CREATE TABLE cliente (
 id_cliente INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE, telefone VARCHAR(30)
);
CREATE TABLE carrinho (
 id_carrinho INT AUTO_INCREMENT PRIMARY KEY, id_cliente INT NULL,
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);
CREATE TABLE item_carrinho (
 id_item_carrinho INT AUTO_INCREMENT PRIMARY KEY, id_carrinho INT NOT NULL, id_produto INT NOT NULL,
 quantidade INT NOT NULL DEFAULT 1, preco_unitario DECIMAL(10,2) NOT NULL,
 CHECK (quantidade > 0), CHECK (preco_unitario >= 0),
 UNIQUE (id_carrinho,id_produto),
 FOREIGN KEY (id_carrinho) REFERENCES carrinho(id_carrinho) ON DELETE CASCADE,
 FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
);
CREATE TABLE pedido (
 id_pedido INT AUTO_INCREMENT PRIMARY KEY, id_cliente INT NULL,
 criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, status VARCHAR(30) NOT NULL DEFAULT 'PENDENTE',
 total DECIMAL(10,2) NOT NULL DEFAULT 0, cep VARCHAR(9), comentario TEXT,
 CHECK (total >= 0), FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);
CREATE TABLE item_pedido (
 id_item_pedido INT AUTO_INCREMENT PRIMARY KEY, id_pedido INT NOT NULL, id_produto INT NOT NULL,
 nome_produto_snapshot VARCHAR(120) NOT NULL, quantidade INT NOT NULL, preco_unitario DECIMAL(10,2) NOT NULL,
 CHECK (quantidade > 0), CHECK (preco_unitario >= 0),
 FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
 FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
);
CREATE TABLE pagamento (
 id_pagamento INT AUTO_INCREMENT PRIMARY KEY, id_pedido INT NOT NULL UNIQUE,
 metodo ENUM('CARTAO','PIX') NOT NULL, status VARCHAR(30) NOT NULL DEFAULT 'PENDENTE',
 referencia_transacao VARCHAR(100), criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE
);
CREATE TABLE avaliacao (
 id_avaliacao INT AUTO_INCREMENT PRIMARY KEY, id_produto INT NULL, nome_exibicao VARCHAR(120) NOT NULL,
 comentario TEXT, estrelas TINYINT NOT NULL, criada_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CHECK (estrelas BETWEEN 1 AND 5),
 FOREIGN KEY (id_produto) REFERENCES produto(id_produto) ON DELETE SET NULL
);
