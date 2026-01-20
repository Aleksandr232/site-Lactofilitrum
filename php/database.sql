-- SQL скрипт для создания базы данных и таблицы пользователей

-- Создание базы данных
CREATE DATABASE IF NOT EXISTS lactofilitrum_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Использование базы данных
USE lactofilitrum_db;

-- Создание таблицы пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Создание индексов для оптимизации
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);

-- Вставка администратора по умолчанию
-- Пароль: admin123 (захеширован с помощью password_hash)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@lactofilitrum.com', 'admin')
ON DUPLICATE KEY UPDATE
    password = VALUES(password),
    email = VALUES(email),
    role = VALUES(role);

-- Создание таблицы для логов авторизации (опционально)
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Создание индекса для логов
CREATE INDEX idx_login_time ON login_logs(login_time);
CREATE INDEX idx_user_id ON login_logs(user_id);