<?php
// Скрипт проверки авторизации

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка авторизации
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Если это AJAX запрос
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Не авторизован']);
        exit();
    }
    // Если обычный запрос - перенаправляем на страницу входа
    else {
        header('Location: ../login.html');
        exit();
    }
}

// Проверка истечения сессии
if (isset($_SESSION['expire']) && time() > $_SESSION['expire']) {
    session_destroy();
    header('Location: ../login.html');
    exit();
}

// Проверка существования пользователя в базе данных
try {
    require_once 'config.php';
    $conn = connectDB();

    $stmt = $conn->prepare("SELECT id, username, role, is_active FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !$user['is_active']) {
        session_destroy();
        header('Location: ../login.html');
        exit();
    }

    // Обновляем данные сессии
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];

} catch (PDOException $e) {
    error_log("Ошибка проверки авторизации: " . $e->getMessage());
    session_destroy();
    header('Location: ../login.html');
    exit();
}
?>