-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS tienda_frutas;

-- Usar la base de datos
USE tienda_frutas;

-- Crear tabla de frutas
CREATE TABLE IF NOT EXISTS frutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255),
    stock INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar algunos datos de ejemplo
INSERT INTO frutas (nombre, descripcion, precio, stock) VALUES
('Manzana', 'Manzana roja fresca y jugosa', 1.50, 100),
('Plátano', 'Plátano amarillo y maduro', 0.80, 150),
('Naranja', 'Naranja dulce y jugosa', 1.20, 80),
('Fresa', 'Fresas frescas de temporada', 2.50, 50),
('Piña', 'Piña tropical dulce', 3.50, 30);