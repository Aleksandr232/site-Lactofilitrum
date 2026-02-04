<?php
// API для управления подкастами
// GET — без авторизации (для слайдера на главной); POST, DELETE — только для админов
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

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

// Функция для удаления файла изображения или видео
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

// Функция для загрузки видео подкаста
function uploadVideo($file, $folder = null) {
    if ($folder === null) {
        $folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/videos/';
    }
    $relativeFolder = 'uploads/podcasts/videos/';

    if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    if (!file_exists($folder)) {
        if (!@mkdir($folder, 0777, true) && !@mkdir($folder, 0755, true)) {
            return '';
        }
    }

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $maxSize = 100 * 1024 * 1024; // 100 MB
    if ($fileSize > $maxSize) {
        return '';
    }

    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['mp4', 'webm', 'ogv', 'mov'];
    if (!in_array($extension, $allowedExtensions)) {
        return '';
    }

    $filename = uniqid('podcast_video_', true) . '.' . $extension;
    $filepath = $folder . $filename;

    if (move_uploaded_file($fileTmpName, $filepath)) {
        return $relativeFolder . $filename;
    }
    if (copy($fileTmpName, $filepath)) {
        @unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    $content = @file_get_contents($fileTmpName);
    if ($content !== false && @file_put_contents($filepath, $content) !== false) {
        @unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    return '';
}

// Функция для загрузки аудио подкаста
function uploadAudio($file, $folder = null) {
    if ($folder === null) {
        $folder = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/audio/';
    }
    $relativeFolder = 'uploads/podcasts/audio/';

    if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    if (!file_exists($folder)) {
        if (!@mkdir($folder, 0777, true) && !@mkdir($folder, 0755, true)) {
            return '';
        }
    }

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $maxSize = 100 * 1024 * 1024; // 100 MB
    if ($fileSize > $maxSize) {
        return '';
    }

    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
    if (!in_array($extension, $allowedExtensions)) {
        return '';
    }

    $filename = uniqid('podcast_audio_', true) . '.' . $extension;
    $filepath = $folder . $filename;

    if (move_uploaded_file($fileTmpName, $filepath)) {
        return $relativeFolder . $filename;
    }
    if (copy($fileTmpName, $filepath)) {
        @unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    $content = @file_get_contents($fileTmpName);
    if ($content !== false && @file_put_contents($filepath, $content) !== false) {
        @unlink($fileTmpName);
        return $relativeFolder . $filename;
    }
    return '';
}

// Создаем необходимые папки заранее
$uploadBase = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
$uploadPodcasts = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/';
$uploadPodcastsVideos = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/videos/';
$uploadPodcastsAudio = $_SERVER['DOCUMENT_ROOT'] . '/uploads/podcasts/audio/';

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

if (!file_exists($uploadPodcastsVideos)) {
    mkdir($uploadPodcastsVideos, 0777, true);
    error_log('Created podcasts videos upload folder');
}

if (!file_exists($uploadPodcastsAudio)) {
    mkdir($uploadPodcastsAudio, 0777, true);
    error_log('Created podcasts audio upload folder');
}

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    require_once __DIR__ . '/../auth_check.php';
}

try {
    $conn = connectDB();

    switch ($method) {
        case 'GET':
            $slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($id > 0) {
                // Один подкаст по id (для редактирования)
                $stmt = $conn->prepare("SELECT * FROM podcasts WHERE id = ? LIMIT 1");
                $stmt->execute([$id]);
                $podcast = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($podcast) {
                    echo json_encode(['success' => true, 'podcast' => $podcast], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Подкаст не найден'], JSON_UNESCAPED_UNICODE);
                }
            } elseif ($slug !== '') {
                // Один подкаст по slug (для single.php)
                $stmt = $conn->prepare("SELECT * FROM podcasts WHERE slug = ? LIMIT 1");
                $stmt->execute([$slug]);
                $podcast = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($podcast) {
                    echo json_encode(['success' => true, 'podcast' => $podcast], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Подкаст не найден'], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // Все подкасты (для слайдера)
                $stmt = $conn->prepare("SELECT * FROM podcasts ORDER BY created_at DESC");
                $stmt->execute();
                $podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'podcasts' => $podcasts], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'POST':
            $editId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $isUpdate = $editId > 0;

            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            // Для HTML контента используем более мягкую очистку, сохраняя HTML теги
            $podcasts_text = isset($_POST['podcasts_text']) ? $_POST['podcasts_text'] : '';
            error_log('podcasts_text raw from POST: ' . (empty($podcasts_text) ? 'EMPTY' : 'length: ' . strlen($podcasts_text) . ', content: ' . substr($podcasts_text, 0, 100)));
            
            // Очищаем от потенциально опасных тегов, но сохраняем безопасные HTML теги
            // Разрешаем больше тегов для полноценного HTML контента
            $allowed_tags = '<p><br><br/><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><div><span><blockquote><pre><code><table><tr><td><th><tbody><thead><tfoot>';
            if (!empty($podcasts_text)) {
                $podcasts_text = strip_tags($podcasts_text, $allowed_tags);
                // Убираем только лишние пробелы, но сохраняем содержимое
                $podcasts_text = trim($podcasts_text);
            }
            error_log('podcasts_text after processing: ' . (empty($podcasts_text) ? 'EMPTY' : 'length: ' . strlen($podcasts_text)));
            $author = sanitize($_POST['author'] ?? '');
            $button_link = sanitize($_POST['button_link'] ?? '');
            $additional_link = sanitize($_POST['additional_link'] ?? '');
            $extra_link = sanitize($_POST['extra_link'] ?? '');

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Название подкаста обязательно']);
                exit;
            }

            $oldImage = '';
            $oldAuthorPhoto = '';
            $oldVideo = '';
            $oldAudio = '';
            if ($isUpdate) {
                $stmt = $conn->prepare("SELECT image, author_photo, video_path, audio_path FROM podcasts WHERE id = ?");
                $stmt->execute([$editId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($existing) {
                    $oldImage = $existing['image'] ?? '';
                    $oldAuthorPhoto = $existing['author_photo'] ?? '';
                    $oldVideo = $existing['video_path'] ?? '';
                    $oldAudio = $existing['audio_path'] ?? '';
                }
            }

            // Загрузка файлов: при обновлении — новый файл или оставляем старый
            $newImg = uploadImage($_FILES['image'] ?? null);
            $newAuth = uploadImage($_FILES['author_photo'] ?? null);
            $newVid = uploadVideo($_FILES['video'] ?? null);
            $newAud = uploadAudio($_FILES['audio'] ?? null);

            if ($isUpdate) {
                $image_path = $newImg ?: $oldImage;
                $author_photo_path = $newAuth ?: $oldAuthorPhoto;
                $video_path = $newVid ?: $oldVideo;
                $audio_path = $newAud ?: $oldAudio;
                if ($newImg && $oldImage) deleteImageFile($oldImage);
                if ($newAuth && $oldAuthorPhoto) deleteImageFile($oldAuthorPhoto);
                if ($newVid && $oldVideo) deleteImageFile($oldVideo);
                if ($newAud && $oldAudio) deleteImageFile($oldAudio);
            } else {
                $image_path = $newImg;
                $author_photo_path = $newAuth;
                $video_path = $newVid;
                $audio_path = $newAud;
            }

            $baseSlug = slugify($title);
            $slug = $baseSlug;
            if (!$isUpdate) {
                $n = 2;
                while (true) {
                    $stmt = $conn->prepare("SELECT id FROM podcasts WHERE slug = ? LIMIT 1");
                    $stmt->execute([$slug]);
                    if (!$stmt->fetch()) break;
                    $slug = $baseSlug . '-' . $n;
                    $n++;
                }
            } else {
                $stmt = $conn->prepare("SELECT slug FROM podcasts WHERE id = ?");
                $stmt->execute([$editId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $slug = $row ? $row['slug'] : $baseSlug;
            }

            $podcasts_text_value = isset($_POST['podcasts_text']) && $_POST['podcasts_text'] !== '' ? $podcasts_text : null;

            if ($isUpdate) {
                $stmt = $conn->prepare("
                    UPDATE podcasts SET title=?, description=?, podcasts_text=?, image=?, author=?, author_photo=?,
                    button_link=?, additional_link=?, extra_link=?, video_path=?, audio_path=? WHERE id=?
                ");
                $result = $stmt->execute([$title, $description, $podcasts_text_value, $image_path, $author, $author_photo_path, $button_link, $additional_link, $extra_link ?: null, $video_path ?: null, $audio_path ?: null, $editId]);
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Подкаст успешно обновлён']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении подкаста']);
                }
            } else {
                try {
                    $checkAudioField = $conn->query("SHOW COLUMNS FROM podcasts LIKE 'audio_path'");
                    if ($checkAudioField->rowCount() === 0) {
                        $conn->exec("ALTER TABLE podcasts ADD COLUMN audio_path VARCHAR(500) DEFAULT NULL AFTER video_path");
                    }
                } catch (PDOException $e) {
                    error_log('Error checking audio_path: ' . $e->getMessage());
                }
                $stmt = $conn->prepare("
                    INSERT INTO podcasts (title, slug, description, podcasts_text, image, author, author_photo, button_link, additional_link, extra_link, video_path, audio_path)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $result = $stmt->execute([$title, $slug, $description, $podcasts_text_value, $image_path, $author, $author_photo_path, $button_link, $additional_link, $extra_link ?: null, $video_path ?: null, $audio_path ?: null]);
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Подкаст успешно добавлен']);
                } else {
                    deleteImageFile($image_path);
                    deleteImageFile($author_photo_path);
                    deleteImageFile($video_path);
                    deleteImageFile($audio_path);
                    echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении подкаста']);
                }
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
            $stmt = $conn->prepare("SELECT image, author_photo, video_path, audio_path FROM podcasts WHERE id = ?");
            $stmt->execute([$id]);
            $podcast = $stmt->fetch();

            // Удаляем файлы изображений, видео и аудио
            if ($podcast) {
                deleteImageFile($podcast['image']);
                deleteImageFile($podcast['author_photo']);
                deleteImageFile($podcast['video_path'] ?? '');
                deleteImageFile($podcast['audio_path'] ?? '');
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