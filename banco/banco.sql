-- Criação do Banco de Dados (substitua pelo nome do banco do cPanel se necessário)
CREATE DATABASE IF NOT EXISTS gestaoong DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestaoong;

-- 1. Tabela de Usuários (Administradores e Beneficiários)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'beneficiario', 'voluntario') DEFAULT 'beneficiario',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabela de Famílias (Gestão de Assistidos)
CREATE TABLE IF NOT EXISTS familias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    responsavel_id INT NOT NULL,
    nome_responsavel VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    endereco TEXT NOT NULL,
    quantidade_membros INT DEFAULT 1,
    renda_familiar DECIMAL(10,2),
    status ENUM('ativo', 'analise', 'inativo') DEFAULT 'analise',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Tabela de Solicitações (Pedidos de ajuda/cestas/recursos)
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    familia_id INT NOT NULL,
    descricao TEXT NOT NULL,
    categoria VARCHAR(50) NOT NULL, -- Ex: Cesta Básica, Medicamento, Roupa
    status ENUM('pendente', 'aprovado', 'entregue', 'cancelado') DEFAULT 'pendente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (familia_id) REFERENCES familias(id) ON DELETE CASCADE
);

-- 4. Tabela de Doações (Entradas de recursos ou mantimentos)
CREATE TABLE IF NOT EXISTS doacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doador_nome VARCHAR(100),
    tipo_doacao VARCHAR(50) NOT NULL, -- Ex: Alimento, Financeiro, Roupa
    quantidade_ou_valor VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_doacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabela de Ações (Eventos ou campanhas da ONG)
CREATE TABLE IF NOT EXISTS acoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    data_acao DATE NOT NULL,
    local VARCHAR(150),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);