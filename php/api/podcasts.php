<?php
// API для управления подкастами
require_once '../auth_check.php';

header('Content-Type: application/json');

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = connectDB();

    switch ($method) {
        case 'GET':
            // Получить все подкасты
            $stmt = $conn->prepare("SELECT * FROM podcasts ORDER BY created_at DESC");
            $stmt->execute();
            $podcasts = $stmt->fetchAll();

            echo json_encode(['success' => true, 'podcasts' => $podcasts]);
            break;

        case 'POST':
            // Создать новый подкаст
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                $data = $_POST;
            }

            $title = sanitize($data['title'] ?? '');
            $description = sanitize($data['description'] ?? '');
            $image = sanitize($data['image'] ?? '');
            $author = sanitize($data['author'] ?? '');
            $author_photo = sanitize($data['author_photo'] ?? '');
            $button_link = sanitize($data['button_link'] ?? '');
            $additional_link = sanitize($data['additional_link'] ?? '');

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Название подкаста обязательно']);
                exit;
            }

            $stmt = $conn->prepare("
                INSERT INTO podcasts (title, description, image, author, author_photo, button_link, additional_link)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([$title, $description, $image, $author, $author_photo, $button_link, $additional_link]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Подкаст успешно добавлен']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении подкаста']);
            }
            break;

        case 'DELETE':
            // Удалить подкаст
            $id = $_GET['id'] ?? 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID подкаста не указан']);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM podcasts WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Подкаст успешно удален']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при удалении подкаста']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            break;
    }

} catch (PDOException $e) {
    error_log("Ошибка API подкастов: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
} catch (Exception $e) {
    error_log("Общая ошибка API подкастов: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>