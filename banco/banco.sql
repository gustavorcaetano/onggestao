CREATE DATABASE IF NOT EXISTS onggestao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE onggestao;

-- 1. Tabela de Usuários (Admin, Doador, Beneficiário)
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'beneficiario', 'doador') NOT NULL DEFAULT 'beneficiario',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabela de Famílias Assistidas
DROP TABLE IF EXISTS familias;
CREATE TABLE familias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responsavel_nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(20) NULL,
    cep VARCHAR(9) NULL,
    rua VARCHAR(150) NULL,
    numero VARCHAR(20) NULL,
    bairro VARCHAR(100) NULL,
    cidade VARCHAR(100) NULL,
    estado VARCHAR(2) NULL,
    dependentes INT DEFAULT 0,
    renda_mensal DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Em Análise', 'Aprovado', 'Pendente', 'Cancelado') DEFAULT 'Em Análise',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabela de Doações
DROP TABLE IF EXISTS doacoes;
CREATE TABLE doacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doador_nome VARCHAR(100) NOT NULL,
    tipo_doacao VARCHAR(50) NOT NULL,
    quantidade_ou_valor VARCHAR(50) NOT NULL,
    descricao TEXT NULL,
    data_doacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabela de Solicitações (Pedidos dos Beneficiários)
DROP TABLE IF EXISTS solicitacoes;
CREATE TABLE solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    solicitante_nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('Pendente', 'Em Análise', 'Aprovado', 'Entregue', 'Recusado') DEFAULT 'Pendente',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- 5. Tabela de Ações e Projetos da ONG (Mural / Feed da Home)
DROP TABLE IF EXISTS acoes;
CREATE TABLE acoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    imagem VARCHAR(255) NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Inserir Usuário Administrador Padrão (Senha: admin123)
-- Nota: Em ambiente real, insira a senha hash gerada por password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@ong.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1g.N8.3G/pM3JpB0X6U6L2jH5.n5EOG', 'admin')
ON DUPLICATE KEY UPDATE id=id;