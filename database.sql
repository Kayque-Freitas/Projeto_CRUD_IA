-- Script para as tabelas do projeto
-- Nota: No InfinityFree, crie o banco manualmente no painel e então importe este arquivo.

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('ADMIN', 'USER') NOT NULL DEFAULT 'USER',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserindo o usuário ADMIN padrão
-- Senha gerada com password_hash('admin123', PASSWORD_DEFAULT);
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@admin.com', '$2y$10$Uo2hL.3y/VbXItk5W5PZMeCg8Q3r2p8rI1F4wR1eO7tGZ4e.mQ4Yq', 'ADMIN');
