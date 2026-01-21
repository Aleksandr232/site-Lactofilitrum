<?php
// API для управления подкастами
require_once '../auth_check.php';

header('Content-Type: application/json');

// Функция для обработки загруженного изображения
function uploadImage($file, $folder = null) {
    // Используем путь относительно DOCUMENT_ROOT для сохранения файла
    if ($folder === null) {
        $folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/';
    }

    // Относительный путь для сохранения в БД
    $relativeFolder = 'uploads/podcasts/';
    error_log('uploadImage called with file: ' . print_r($file, true));

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        error_log('File not set or error: ' . ($file['error'] ?? 'not set'));
        return '';
    }

    // Создаем папку, если не существует
    if (!file_exists($folder)) {
        error_log('Creating folder: ' . $folder);
        // Пробуем создать папку с правами 0777
        if (!mkdir($folder, 0777, true)) {
            error_log('Failed to create folder with 0777 permissions: ' . $folder);
            // Пробуем с 0755
            if (!mkdir($folder, 0755, true)) {
                error_log('Failed to create folder with 0755 permissions: ' . $folder);
                return '';
            }
        }
        error_log('Folder created successfully');
    } else {
        error_log('Folder already exists: ' . $folder);
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

    error_log('Attempting to move file from: ' . $fileTmpName . ' to: ' . $filepath);
    error_log('File exists at tmp: ' . (file_exists($fileTmpName) ? 'YES' : 'NO'));
    error_log('Target folder writable: ' . (is_writable($folder) ? 'YES' : 'NO'));
    error_log('File size: ' . $fileSize . ' bytes');

    // Пробуем разные способы перемещения файла
    $moved = false;

    // Способ 1: move_uploaded_file
    if (move_uploaded_file($fileTmpName, $filepath)) {
        $moved = true;
        error_log('File moved successfully with move_uploaded_file');
    } else {
        error_log('move_uploaded_file failed, trying copy...');

        // Способ 2: copy + unlink
        if (copy($fileTmpName, $filepath)) {
            unlink($fileTmpName);
            $moved = true;
            error_log('File moved successfully with copy+unlink');
        } else {
            error_log('copy failed, trying file_put_contents...');

            // Способ 3: file_put_contents
            $fileContent = file_get_contents($fileTmpName);
            if ($fileContent !== false && file_put_contents($filepath, $fileContent) !== false) {
                unlink($fileTmpName);
                $moved = true;
                error_log('File moved successfully with file_put_contents');
            }
        }
    }

    if ($moved) {
        error_log('File successfully saved to: ' . $filepath);
        // Возвращаем относительный путь для сохранения в БД
        return $relativeFolder . $filename;
    }

    error_log('All file moving methods failed. Last error: ' . error_get_last()['message'] ?? 'unknown');
    return '';
}

// Функция для удаления файла изображения
function deleteImageFile($filepath) {
    if (!empty($filepath)) {
        // Если путь относительный, преобразуем в полный
        if (strpos($filepath, 'uploads/') === 0) {
            $filepath = $_SERVER['DOCUMENT_ROOT'] . '/' . $filepath;
        }

        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}

// Создаем необходимые папки заранее
$uploadBase = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
$uploadPodcasts = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/';

error_log('DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT']);
error_log('Upload base path: ' . $uploadBase);
error_log('Upload podcasts path: ' . $uploadPodcasts);

if (!file_exists($uploadBase)) {
    mkdir($uploadBase, 0777, true);
    error_log('Created uploads base folder');
}

if (!file_exists($uploadPodcasts)) {
    mkdir($uploadPodcasts, 0777, true);
    error_log('Created podcasts upload folder');
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

            // Логируем полученные файлы для отладки
            error_log('FILES received: ' . print_r($_FILES, true));
            error_log('POST data: ' . print_r($_POST, true));

            // Обрабатываем загруженные изображения
            $image_path = uploadImage($_FILES['image'] ?? null);
            $author_photo_path = uploadImage($_FILES['author_photo'] ?? null);

            error_log('Image path result: ' . $image_path);
            error_log('Author photo path result: ' . $author_photo_path);

            // Для отладки - возвращаем информацию о загрузке
            $debug_info = [
                'files_received' => count($_FILES),
                'image_path' => $image_path,
                'author_photo_path' => $author_photo_path,
                'upload_folder_exists' => file_exists(__DIR__ . '/../../uploads/podcasts/'),
                'upload_folder_writable' => is_writable(__DIR__ . '/../../uploads/podcasts/')
            ];
            error_log('Debug info: ' . json_encode($debug_info));

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