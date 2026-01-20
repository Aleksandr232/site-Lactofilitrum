<?php
// Скрипт для ручного создания администратора
require_once 'php/config.php';

try {
    $conn = connectDB();

    // Проверяем текущую базу данных
    $stmt = $conn->prepare("SELECT DATABASE() as current_db");
    $stmt->execute();
    $currentDb = $stmt->fetch();

    echo "<h1>Создание администратора</h1>";
    echo "<p>Текущая база данных: <strong>" . $currentDb['current_db'] . "</strong></p>";

    // Проверяем, существует ли уже администратор
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        echo "<p style='color: green;'>✓ Администратор уже существует:</p>";
        echo "<ul>";
        echo "<li>ID: " . $admin['id'] . "</li>";
        echo "<li>Логин: " . $admin['username'] . "</li>";
        echo "<li>Email: " . $admin['email'] . "</li>";
        echo "<li>Роль: " . $admin['role'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>Администратор не найден, создаем...</p>";

        // Создаем администратора
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute(['admin', $hashedPassword, 'admin@lactofilitrum.com', 'admin']);

        if ($result) {
            echo "<p style='color: green;'>✓ Администратор успешно создан!</p>";
            echo "<p><strong>Данные для входа:</strong></p>";
            echo "<ul>";
            echo "<li><strong>Логин:</strong> admin</li>";
            echo "<li><strong>Пароль:</strong> admin123</li>";
            echo "</ul>";
            echo "<p><a href='/login'>Перейти к авторизации</a></p>";
        } else {
            echo "<p style='color: red;'>✗ Ошибка при создании администратора</p>";
        }
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Ошибка базы данных: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Общая ошибка: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>← Вернуться на главную</a></p>";
echo "<p><a href='php/init.php'>Проверить статус базы данных</a></p>";
?>