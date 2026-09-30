CREATE DATABASE IF NOT EXISTS agenda_db_luis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agenda_db_luis;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    category ENUM('Familia', 'Trabajo', 'Amigos', 'Otros') DEFAULT 'Otros',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);