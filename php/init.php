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
        function checkTables() {
            try {
                $conn = connectDB();

                $tables = ['users', 'login_logs'];
                $missingTables = [];

                foreach ($tables as $table) {
                    $result = $conn->query("SHOW TABLES LIKE '" . $conn->quote($table) . "'");
                    if (!$result->fetch()) {
                        $missingTables[] = $table;
                    }
                }

                if (empty($missingTables)) {
                    showStatus("✓ Все необходимые таблицы существуют", "success");
                    return true;
                } else {
                    showStatus("⚠ Отсутствуют таблицы: " . implode(', ', $missingTables), "error");
                    return false;
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

                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
                $stmt->execute();
                $result = $stmt->fetch();

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
        } else {
            // Проверяем текущее состояние
            showStatus("🔍 Проверяем состояние базы данных...", "info");

            $connectionOk = testConnection();
            $tablesOk = $connectionOk ? checkTables() : false;
            $adminOk = $connectionOk ? checkAdmin() : false;

            if ($connectionOk && $tablesOk && $adminOk) {
                showStatus("✅ База данных полностью настроена и готова к работе!", "success");
                echo "<p><a href='/login' class='btn'>Перейти к авторизации</a></p>";
                echo "<p><a href='/' class='btn btn-secondary'>Перейти на сайт</a></p>";
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