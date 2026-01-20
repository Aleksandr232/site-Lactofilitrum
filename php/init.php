<?php
// Скрипт для инициализации базы данных
// Можно запускать вручную: http://localhost/php/init.php

require_once 'config.php';

// Запуск сессии для логирования
session_start();

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Инициализация базы данных - Lactofilitrum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Инициализация базы данных Lactofilitrum</h1>

        <?php
        // Функция для вывода статуса
        function showStatus($message, $type = 'info') {
            echo "<div class='status $type'>$message</div>";
        }

        // Функция для проверки подключения
        function testConnection() {
            try {
                $conn = connectDB();
                showStatus("✓ Подключение к базе данных успешно", "success");
                return true;
            } catch (Exception $e) {
                showStatus("✗ Ошибка подключения: " . $e->getMessage(), "error");
                return false;
            }
        }

        // Функция для проверки таблиц
        function checkTables($autoCreate = false) {
            try {
                $conn = connectDB();

                $tables = ['users', 'login_logs'];
                $missingTables = [];

                foreach ($tables as $table) {
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    if (!$result->fetch()) {
                        $missingTables[] = $table;
                    }
                }

                if (empty($missingTables)) {
                    showStatus("✓ Все необходимые таблицы существуют", "success");
                    return true;
                } else {
                    if ($autoCreate) {
                        showStatus("⚠ Отсутствуют таблицы: " . implode(', ', $missingTables) . " - создаем...", "info");

                        // Создаем таблицы по отдельности
                        if (in_array('users', $missingTables)) {
                            $conn->exec("
                                CREATE TABLE IF NOT EXISTS users (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    username VARCHAR(50) NOT NULL UNIQUE,
                                    password VARCHAR(255) NOT NULL,
                                    email VARCHAR(100),
                                    role ENUM('admin', 'user') DEFAULT 'user',
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    last_login TIMESTAMP NULL,
                                    is_active BOOLEAN DEFAULT TRUE
                                )
                            ");
                            $conn->exec("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
                            $conn->exec("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
                            showStatus("✓ Таблица users создана", "success");
                        }

                        if (in_array('login_logs', $missingTables)) {
                            $conn->exec("
                                CREATE TABLE IF NOT EXISTS login_logs (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    user_id INT,
                                    username VARCHAR(50),
                                    ip_address VARCHAR(45),
                                    user_agent TEXT,
                                    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    success BOOLEAN DEFAULT FALSE,
                                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                                )
                            ");
                            $conn->exec("CREATE INDEX IF NOT EXISTS idx_login_time ON login_logs(login_time)");
                            $conn->exec("CREATE INDEX IF NOT EXISTS idx_user_id ON login_logs(user_id)");
                            showStatus("✓ Таблица login_logs создана", "success");
                        }

                        // Создаем администратора
                        createDefaultAdmin($conn);
                        showStatus("✓ Администратор создан", "success");

                        return true;
                    } else {
                        showStatus("⚠ Отсутствуют таблицы: " . implode(', ', $missingTables), "error");
                        return false;
                    }
                }
            } catch (Exception $e) {
                showStatus("✗ Ошибка проверки таблиц: " . $e->getMessage(), "error");
                return false;
            }
        }

        // Функция для проверки администратора
        function checkAdmin() {
            try {
                $conn = connectDB();

                // Сначала проверим, что мы в правильной базе данных
                $stmt = $conn->prepare("SELECT DATABASE() as current_db");
                $stmt->execute();
                $currentDb = $stmt->fetch();
                showStatus("Текущая база данных: " . $currentDb['current_db'], "info");

                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
                $stmt->execute();
                $result = $stmt->fetch();

                showStatus("Количество администраторов: " . $result['count'], "info");

                if ($result['count'] > 0) {
                    showStatus("✓ Администратор по умолчанию существует", "success");
                    return true;
                } else {
                    showStatus("⚠ Администратор по умолчанию отсутствует", "error");
                    return false;
                }
            } catch (Exception $e) {
                showStatus("✗ Ошибка проверки администратора: " . $e->getMessage(), "error");
                return false;
            }
        }

        // Основная логика
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initialize'])) {
            showStatus("🔄 Начинаем инициализацию базы данных...", "info");

            // Инициализируем базу данных
            initializeDatabase();

            // Проверяем результат
            if (testConnection() && checkTables() && checkAdmin()) {
                showStatus("✅ Инициализация завершена успешно!", "success");
                echo "<p><strong>Данные для входа в админку:</strong></p>";
                echo "<ul>";
                echo "<li><strong>Логин:</strong> admin</li>";
                echo "<li><strong>Пароль:</strong> admin123</li>";
                echo "</ul>";
                echo "<p><a href='/login' class='btn'>Перейти к авторизации</a></p>";
            } else {
                showStatus("❌ Инициализация не удалась", "error");
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
            showStatus("🔄 Создаем администратора...", "info");

            try {
                $conn = connectDB();
                createDefaultAdmin($conn);
                showStatus("✅ Администратор создан!", "success");

                echo "<p><strong>Данные для входа:</strong></p>";
                echo "<ul>";
                echo "<li><strong>Логин:</strong> admin</li>";
                echo "<li><strong>Пароль:</strong> admin123</li>";
                echo "</ul>";
                echo "<p><a href='/login' class='btn'>Перейти к авторизации</a></p>";
            } catch (Exception $e) {
                showStatus("❌ Ошибка создания администратора: " . $e->getMessage(), "error");
            }
        } else {
            // Проверяем текущее состояние
            showStatus("🔍 Проверяем состояние базы данных...", "info");

            $connectionOk = testConnection();
            $tablesOk = $connectionOk ? checkTables(true) : false; // Автоматически создаем таблицы если отсутствуют
            $adminOk = $connectionOk ? checkAdmin() : false;

            if ($connectionOk && $tablesOk && $adminOk) {
                showStatus("✅ База данных полностью настроена и готова к работе!", "success");
                echo "<p><a href='/login' class='btn'>Перейти к авторизации</a></p>";
                echo "<p><a href='/' class='btn btn-secondary'>Перейти на сайт</a></p>";
            } elseif ($connectionOk && $tablesOk && !$adminOk) {
                showStatus("⚠ База данных настроена, но отсутствует администратор", "error");
                echo "<form method='post' style='display: inline-block; margin-right: 10px;'>";
                echo "<button type='submit' name='create_admin' value='1' class='btn'>Создать администратора</button>";
                echo "</form>";
                echo "<p><a href='../create_admin.php' class='btn btn-secondary'>Ручное создание администратора</a></p>";
                echo "<p><strong>Будет создан пользователь:</strong> admin / admin123</p>";
            } else {
                showStatus("⚠ База данных требует инициализации", "error");
                echo "<form method='post'>";
                echo "<button type='submit' name='initialize' value='1' class='btn'>Инициализировать базу данных</button>";
                echo "</form>";
            }
        }

        // Показываем текущие настройки
        echo "<h2>Текущие настройки</h2>";
        echo "<pre>";
        echo "Хост: " . DB_HOST . "\n";
        echo "База данных: " . DB_NAME . "\n";
        echo "Пользователь: " . DB_USER . "\n";
        echo "Пароль: " . (DB_PASS ? "***" : "(пустой)") . "\n";
        echo "</pre>";
        ?>

        <h2>Что делает инициализация:</h2>
        <ul>
            <li>Создает базу данных <code><?php echo DB_NAME; ?></code>, если она не существует</li>
            <li>Создает необходимые таблицы (users, login_logs)</li>
            <li>Добавляет администратора по умолчанию (admin/admin123)</li>
            <li>Настраивает индексы для оптимизации производительности</li>
        </ul>

        <p><strong>Примечание:</strong> Этот скрипт можно запускать многократно - он не удалит существующие данные.</p>

        <p><a href="/" class="btn btn-secondary">← Вернуться на главную</a></p>
    </div>
</body>
</html>