
CREATE DATABASE IF NOT EXISTS gerenciador_logistico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE gerenciador_logistico;

CREATE TABLE IF NOT EXISTS enderecos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    cep VARCHAR(10) DEFAULT NULL,
    estado CHAR(2) DEFAULT NULL,
    cidade VARCHAR(100) NOT NULL,
    bairro VARCHAR(100) DEFAULT NULL,
    rua VARCHAR(150) DEFAULT NULL,
    numero VARCHAR(20) DEFAULT NULL,
    complemento VARCHAR(100) DEFAULT NULL,
    criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS produtos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT DEFAULT NULL,
    peso DECIMAL(10,2) DEFAULT NULL,
    comprimento DECIMAL(10,2) DEFAULT NULL,
    largura DECIMAL(10,2) DEFAULT NULL,
    altura DECIMAL(10,2) DEFAULT NULL,
    criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS transportadores (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(18) DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS fretes (
    id INT NOT NULL AUTO_INCREMENT,
    produto_id INT NOT NULL,
    origem_id INT NOT NULL,
    destino_id INT NOT NULL,
    transportador_id INT NOT NULL,
    data_envio DATE DEFAULT NULL,
    data_entrega DATE DEFAULT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('Pendente','Em trânsito','Entregue','Cancelado') NOT NULL DEFAULT 'Pendente',
    observacoes TEXT DEFAULT NULL,
    criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY produto_id (produto_id),
    KEY origem_id (origem_id),
    KEY destino_id (destino_id),
    KEY transportador_id (transportador_id),
    CONSTRAINT fretes_ibfk_1 FOREIGN KEY (produto_id) REFERENCES produtos (id),
    CONSTRAINT fretes_ibfk_2 FOREIGN KEY (origem_id) REFERENCES enderecos (id),
    CONSTRAINT fretes_ibfk_3 FOREIGN KEY (destino_id) REFERENCES enderecos (id),
    CONSTRAINT fretes_ibfk_4 FOREIGN KEY (transportador_id) REFERENCES transportadores (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS frete_produtos (
    id INT NOT NULL AUTO_INCREMENT,
    frete_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY frete_produto_unico (frete_id, produto_id),
    KEY produto_id (produto_id),
    CONSTRAINT frete_produtos_ibfk_1 FOREIGN KEY (frete_id) REFERENCES fretes (id) ON DELETE CASCADE,
    CONSTRAINT frete_produtos_ibfk_2 FOREIGN KEY (produto_id) REFERENCES produtos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

