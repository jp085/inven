
-- ============================================================
--  INVEN — Banco de dados simplificado
--  Cole no phpMyAdmin > SQL e clique Executar
-- ============================================================

CREATE DATABASE IF NOT EXISTS inven_db CHARACTER SET utf8mb4;
USE inven_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(120) NOT NULL,
    email      VARCHAR(180) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    criado_em  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS materiais (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT NOT NULL,
    descricao   VARCHAR(255) NOT NULL,
    preco       DECIMAL(10,2) NOT NULL DEFAULT 0,
    fonte       VARCHAR(255) NOT NULL,
    telefone    VARCHAR(30),
    email       VARCHAR(180),
    estoque     DECIMAL(10,2) NOT NULL DEFAULT 0,
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS palavras_chave (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    palavra     VARCHAR(80) NOT NULL,
    FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS movimentacoes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    usuario_id  INT NOT NULL,
    tipo        ENUM('entrada','saida','ajuste') NOT NULL,
    quantidade  DECIMAL(10,2) NOT NULL,
    observacao  TEXT,
    criado_em   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materiais(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id)  REFERENCES usuarios(id)  ON DELETE CASCADE
);
