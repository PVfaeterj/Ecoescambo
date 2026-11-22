CREATE DATABASE IF NOT EXISTS ecoescambo
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ecoescambo;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    marca VARCHAR(100),
    condicao ENUM('novo', 'usado') NOT NULL,
    regiao ENUM('centro', 'zonasul', 'zonanorte', 'zonaoeste') NOT NULL,
    aceita_troca VARCHAR(255) NOT NULL,
    estado ENUM('Em aberto', 'Finalizada') NOT NULL DEFAULT 'Em aberto',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS interesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    interessado_id INT NOT NULL,
    UNIQUE KEY unico_interesse (produto_id, interessado_id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (interessado_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS propostas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interesse_id INT NOT NULL,
    produto_proposto_id INT NOT NULL,
    UNIQUE KEY proposta_unica (interesse_id, produto_proposto_id),
    FOREIGN KEY (interesse_id) REFERENCES interesses(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_proposto_id) REFERENCES produtos(id) ON DELETE CASCADE
);
