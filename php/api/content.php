<?php
// API для управления контентом сайта

require_once '../config.php';
require_once '../auth_check.php';

// Установка заголовков
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = connectDB();

    // Пока используем простое хранение в файлах
    // В реальном приложении лучше использовать базу данных
    $contentFile = '../data/content.json';

    // Создаем папку data если не существует
    if (!file_exists('../data')) {
        mkdir('../data', 0755, true);
    }

    switch ($method) {
        case 'GET':
            // Получение контента
            $content = [];
            if (file_exists($contentFile)) {
                $content = json_decode(file_get_contents($contentFile), true) ?? [];
            }

            echo json_encode(['success' => true, 'content' => $content]);
            break;

        case 'POST':
            // Сохранение контента
            $content = [
                'main' => sanitize($_POST['main'] ?? ''),
                'about' => sanitize($_POST['about'] ?? ''),
                'contact' => sanitize($_POST['contact'] ?? ''),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $_SESSION['username']
            ];

            // Сохраняем в файл
            if (file_put_contents($contentFile, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                echo json_encode(['success' => true, 'message' => 'Контент успешно сохранен']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка сохранения контента']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    }

} catch (Exception $e) {
    error_log("Ошибка: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>