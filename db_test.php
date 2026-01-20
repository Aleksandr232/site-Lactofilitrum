<?php
echo "<h1>Тест подключения к базе данных</h1>";

try {
    $pdo = new PDO(
        "mysql:host=localhost;charset=utf8mb4",
        "cz19567_lacto",
        "AhLiNBc6",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    echo "<p style='color: green;'>✓ Подключение к MySQL успешно</p>";

    // Проверяем базу данных
    $result = $pdo->query("SHOW DATABASES LIKE 'cz19567_lacto'");
    $databaseExists = $result->fetch();

    if ($databaseExists) {
        echo "<p style='color: green;'>✓ База данных cz19567_lacto существует</p>";

        // Пробуем подключиться к базе данных
        $pdo->exec("USE `cz19567_lacto`");
        echo "<p style='color: green;'>✓ Подключение к базе данных успешно</p>";

        // Проверяем таблицы
        $tables = ['users', 'login_logs'];
        foreach ($tables as $table) {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            $tableExists = $result->fetch();
            if ($tableExists) {
                echo "<p style='color: green;'>✓ Таблица $table существует</p>";
            } else {
                echo "<p style='color: red;'>✗ Таблица $table не найдена</p>";
            }
        }

    } else {
        echo "<p style='color: red;'>✗ База данных cz19567_lacto не найдена</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Ошибка: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='index.html'>Вернуться на главную</a>";
?>