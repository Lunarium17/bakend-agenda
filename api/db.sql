CREATE DATABASE IF NOT EXISTS agenda_dbs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_dbs;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_de_usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(512) DEFAULT NULL, -- Aumentado para soportar JWT largos
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(120),
    direccion VARCHAR(255),
    notas TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);