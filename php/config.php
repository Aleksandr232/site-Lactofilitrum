<?php
// Конфигурационный файл для подключения к базе данных

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'cz19567_lacto');
define('DB_USER', 'cz19567_lacto');
define('DB_PASS', 'AhLiNBc6');

// Настройки сайта
define('SITE_NAME', 'Lactofilitrum');
// Автоматическое определение URL сайта
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('SITE_URL', $protocol . $domain);

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Автоматическая настройка cookie_secure для HTTPS
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 1 : 0);

// Функция для инициализации базы данных
function initializeDatabase() {
    try {
        // Подключаемся к MySQL без указания базы данных
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        // Проверяем существование базы данных
        $stmt = $pdo->prepare("SHOW DATABASES LIKE ?");
        $stmt->execute([DB_NAME]);
        $databaseExists = $stmt->fetch();

        if (!$databaseExists) {
            // Создаем базу данных
            $pdo->exec("CREATE DATABASE `" . $pdo->quote(DB_NAME) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            error_log("База данных '" . DB_NAME . "' создана автоматически");

            // Подключаемся к созданной базе данных
            $pdo->exec("USE `" . $pdo->quote(DB_NAME) . "`");

            // Создаем таблицы
            createTables($pdo);
            createDefaultAdmin($pdo);
        } else {
            // База данных существует, проверяем таблицы
            $pdo->exec("USE `" . $pdo->quote(DB_NAME) . "`");
            ensureTablesExist($pdo);
        }

    } catch (PDOException $e) {
        error_log("Ошибка инициализации базы данных: " . $e->getMessage());
        // Не прерываем выполнение, если база данных уже существует
    }
}

// Функция для создания таблиц
function createTables($pdo) {
    $sql = "
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

        CREATE INDEX IF NOT EXISTS idx_username ON users(username);
        CREATE INDEX IF NOT EXISTS idx_email ON users(email);

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

        CREATE INDEX IF NOT EXISTS idx_login_time ON login_logs(login_time);
        CREATE INDEX IF NOT EXISTS idx_user_id ON login_logs(user_id);
    ";

    $pdo->exec($sql);
    error_log("Таблицы базы данных созданы");
}

// Функция для создания администратора по умолчанию
function createDefaultAdmin($pdo) {
    // Проверяем, существует ли уже администратор
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();

    if (!$adminExists) {
        // Пароль: admin123 (захеширован с помощью password_hash)
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@lactofilitrum.com', 'admin']);

        error_log("Администратор по умолчанию создан (логин: admin, пароль: admin123)");
    }
}

// Функция для проверки существования таблиц
function ensureTablesExist($pdo) {
    $requiredTables = ['users', 'login_logs'];

    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $tableExists = $stmt->fetch();

        if (!$tableExists) {
            // Создаем недостающие таблицы
            createTables($pdo);
            break;
        }
    }

    // Проверяем, есть ли администратор
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();

    if (!$adminExists) {
        createDefaultAdmin($pdo);
    }
}

// Функция для подключения к базе данных
function connectDB() {
    static $conn = null;

    if ($conn === null) {
        // Инициализируем базу данных при первом подключении
        initializeDatabase();

        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    return $conn;
}

// Функция для очистки входных данных
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Функция для проверки авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Функция для проверки роли администратора
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Функция для перенаправления
function redirect($url) {
    header("Location: $url");
    exit();
}

// Функция для генерации CSRF токена
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Функция для проверки CSRF токена
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>