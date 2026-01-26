<?php
// Тестовый скрипт для проверки поля podcasts_text
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $conn = connectDB();
    
    // Проверяем структуру таблицы
    echo "<h2>Структура таблицы podcasts:</h2>";
    $stmt = $conn->query("DESCRIBE podcasts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Поле</th><th>Тип</th><th>Null</th><th>Ключ</th><th>По умолчанию</th></tr>";
    $hasPodcastsText = false;
    foreach ($columns as $column) {
        $isPodcastsText = ($column['Field'] === 'podcasts_text');
        if ($isPodcastsText) {
            $hasPodcastsText = true;
            echo "<tr style='background-color: #d4edda;'>";
        } else {
            echo "<tr>";
        }
        echo "<td><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if (!$hasPodcastsText) {
        echo "<p style='color: red;'><strong>ВНИМАНИЕ: Поле podcasts_text отсутствует в таблице!</strong></p>";
        echo "<p>Попытка добавить поле...</p>";
        try {
            $conn->exec("ALTER TABLE podcasts ADD COLUMN podcasts_text TEXT DEFAULT NULL AFTER description");
            echo "<p style='color: green;'>Поле podcasts_text успешно добавлено!</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Ошибка добавления поля: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color: green;'><strong>✓ Поле podcasts_text существует в таблице</strong></p>";
    }
    
    // Показываем последние подкасты
    echo "<h2>Последние подкасты (показываем первые 5):</h2>";
    $stmt = $conn->query("SELECT id, title, 
        CASE 
            WHEN podcasts_text IS NULL THEN 'NULL'
            WHEN podcasts_text = '' THEN 'EMPTY STRING'
            ELSE CONCAT('HAS TEXT (', LENGTH(podcasts_text), ' chars)')
        END as text_status,
        LEFT(podcasts_text, 100) as text_preview
        FROM podcasts 
        ORDER BY created_at DESC 
        LIMIT 5");
    $podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($podcasts)) {
        echo "<p>Нет подкастов в базе данных.</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Название</th><th>Статус podcasts_text</th><th>Превью текста</th></tr>";
        foreach ($podcasts as $podcast) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($podcast['id']) . "</td>";
            echo "<td>" . htmlspecialchars($podcast['title']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($podcast['text_status']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($podcast['text_preview'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Ошибка:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    error_log("Ошибка теста podcasts_text: " . $e->getMessage());
}
?>
