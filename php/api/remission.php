<?php
// API для управления библиотекой ремиссии
require_once '../auth_check.php';

header('Content-Type: application/json');

// Функция для обработки загруженного изображения
function uploadImage($file, $folder = null) {
    // Используем путь относительно DOCUMENT_ROOT для сохранения файла
    if ($folder === null) {
        $folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/remission/';
    }

    // Относительный путь для сохранения в БД
    $relativeFolder = 'uploads/remission/';
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
    $filename = uniqid('remission_', true) . '.' . $extension;
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
    deleteFile($filepath);
}

// Функция для удаления любого файла
function deleteFile($filepath) {
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

// Функция для обработки загруженного PDF
function uploadPdf($file, $folder = null) {
    if ($folder === null) {
        $folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/remission/';
    }

    $relativeFolder = 'uploads/remission/';

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];

    // Максимум 50MB для PDF
    if ($fileSize > 50 * 1024 * 1024) {
        return '';
    }

    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        return '';
    }

    // Проверяем MIME-тип
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);
    if ($mimeType !== 'application/pdf') {
        return '';
    }

    $filename = uniqid('remission_pdf_', true) . '.pdf';
    $filepath = $folder . $filename;

    if (move_uploaded_file($fileTmpName, $filepath)) {
        return $relativeFolder . $filename;
    }
    if (copy($fileTmpName, $filepath)) {
        unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    $fileContent = file_get_contents($fileTmpName);
    if ($fileContent !== false && file_put_contents($filepath, $fileContent) !== false) {
        unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    return '';
}

// Создаем необходимые папки заранее
$uploadBase = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
$uploadRemission = $_SERVER['DOCUMENT_ROOT'] . '/uploads/remission/';

error_log('DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT']);
error_log('Upload base path: ' . $uploadBase);
error_log('Upload remission path: ' . $uploadRemission);

if (!file_exists($uploadBase)) {
    mkdir($uploadBase, 0777, true);
    error_log('Created uploads base folder');
}

if (!file_exists($uploadRemission)) {
    mkdir($uploadRemission, 0777, true);
    error_log('Created remission upload folder');
}

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = connectDB();

    switch ($method) {
        case 'GET':
            // Получить все элементы библиотеки ремиссии
            $stmt = $conn->prepare("SELECT * FROM remission_library ORDER BY created_at DESC");
            $stmt->execute();
            $items = $stmt->fetchAll();

            echo json_encode(['success' => true, 'items' => $items]);
            break;

        case 'POST':
            // Создать новый элемент
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Название обязательно']);
                exit;
            }

            // Логируем полученные файлы для отладки
            error_log('FILES received: ' . print_r($_FILES, true));
            error_log('POST data: ' . print_r($_POST, true));

            // Обрабатываем загруженное изображение
            $image_path = uploadImage($_FILES['image'] ?? null);
            // Обрабатываем загруженный PDF
            $pdf_path = uploadPdf($_FILES['pdf'] ?? null);

            $stmt = $conn->prepare("
                INSERT INTO remission_library (title, description, image, pdf_path)
                VALUES (?, ?, ?, ?)
            ");

            $result = $stmt->execute([$title, $description, $image_path, $pdf_path]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Элемент успешно добавлен']);
            } else {
                deleteImageFile($image_path);
                deleteFile($pdf_path);
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении элемента']);
            }
            break;

        case 'DELETE':
            // Удалить элемент
            $id = $_GET['id'] ?? 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID элемента не указан']);
                exit;
            }

            // Сначала получаем данные элемента для удаления файлов
            $stmt = $conn->prepare("SELECT image, pdf_path FROM remission_library WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if ($item) {
                deleteImageFile($item['image']);
                deleteFile($item['pdf_path'] ?? '');
            }

            // Удаляем запись из базы данных
            $stmt = $conn->prepare("DELETE FROM remission_library WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Элемент успешно удален']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при удалении элемента']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            break;
    }

} catch (PDOException $e) {
    error_log("Ошибка API remission: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
} catch (Exception $e) {
    error_log("Общая ошибка API remission: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>