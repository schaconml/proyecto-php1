-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS app_taller_php
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE app_taller_php;

-- Creación de la tabla para la calculadora
CREATE TABLE IF NOT EXISTS historial_calculadora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_1 DECIMAL(10,4) NOT NULL,
    operacion VARCHAR(20) NOT NULL,
    numero_2 DECIMAL(10,4),
    resultado DECIMAL(10,4) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);