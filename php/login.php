<?php
// Скрипт обработки авторизации

require_once 'config.php';

// Запуск сессии
session_start();

// Установка заголовков для AJAX запросов
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit();
}

// Получение и очистка данных
$username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Валидация входных данных
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Пожалуйста, заполните все поля']);
    exit();
}

try {
    $conn = connectDB();

    // Поиск пользователя в базе данных
    $stmt = $conn->prepare("SELECT id, username, password, role, is_active FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    $success = false;
    $message = 'Неверный логин или пароль';

    if ($user) {
        // Проверка активности аккаунта
        if (!$user['is_active']) {
            $message = 'Аккаунт заблокирован';
        }
        // Проверка пароля
        elseif (password_verify($password, $user['password'])) {
            $success = true;
            $message = 'Вход выполнен успешно';

            // Обновление времени последнего входа
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Сохранение данных в сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();

            // Установка времени жизни сессии (24 часа)
            $_SESSION['expire'] = time() + (24 * 60 * 60);
        }
    }

    // Логирование попытки входа
    $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, username, ip_address, user_agent, success) VALUES (?, ?, ?, ?, ?)");
    $logStmt->execute([
        $user ? $user['id'] : null,
        $username,
        $ip,
        $userAgent,
        $success
    ]);

    echo json_encode(['success' => $success, 'message' => $message]);

} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
} catch (Exception $e) {
    error_log("Общая ошибка: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>