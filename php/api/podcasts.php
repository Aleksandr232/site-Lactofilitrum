<?php
// API для управления подкастами
require_once '../auth_check.php';

header('Content-Type: application/json');

// Функция для обработки загруженного изображения
function uploadImage($file, $folder = 'uploads/podcasts/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    // Создаем папку, если не существует
    if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
    }

    // Получаем информацию о файле
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];

    // Проверяем размер файла (максимум 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        return '';
    }

    // Получаем расширение файла
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Разрешенные расширения
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $allowedExtensions)) {
        return '';
    }

    // Проверяем, что это действительно изображение
    $imageInfo = getimagesize($fileTmpName);
    if ($imageInfo === false) {
        return '';
    }

    // Генерируем уникальное имя файла
    $filename = uniqid('podcast_', true) . '.' . $extension;
    $filepath = $folder . $filename;

    // Перемещаем файл
    if (move_uploaded_file($fileTmpName, $filepath)) {
        return $filepath;
    }

    return '';
}

// Функция для удаления файла изображения
function deleteImageFile($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        unlink($filepath);
    }
}

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
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $author = sanitize($_POST['author'] ?? '');
            $button_link = sanitize($_POST['button_link'] ?? '');
            $additional_link = sanitize($_POST['additional_link'] ?? '');

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Название подкаста обязательно']);
                exit;
            }

            // Обрабатываем загруженные изображения
            $image_path = uploadImage($_FILES['image'] ?? null);
            $author_photo_path = uploadImage($_FILES['author_photo'] ?? null);

            $stmt = $conn->prepare("
                INSERT INTO podcasts (title, description, image, author, author_photo, button_link, additional_link)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([$title, $description, $image_path, $author, $author_photo_path, $button_link, $additional_link]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Подкаст успешно добавлен']);
            } else {
                // Если не удалось сохранить в БД, удаляем загруженные файлы
                deleteImageFile($image_path);
                deleteImageFile($author_photo_path);
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

            // Сначала получаем данные подкаста для удаления файлов
            $stmt = $conn->prepare("SELECT image, author_photo FROM podcasts WHERE id = ?");
            $stmt->execute([$id]);
            $podcast = $stmt->fetch();

            // Удаляем файлы изображений
            if ($podcast) {
                deleteImageFile($podcast['image']);
                deleteImageFile($podcast['author_photo']);
            }

            // Удаляем запись из базы данных
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