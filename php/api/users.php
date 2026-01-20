<?php
// API для управления пользователями

require_once '../config.php';
require_once '../auth_check.php';

// Установка заголовков
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $conn = connectDB();

    switch ($method) {
        case 'GET':
            // Получение списка пользователей
            $stmt = $conn->prepare("SELECT id, username, email, role, is_active, last_login FROM users ORDER BY id");
            $stmt->execute();
            $users = $stmt->fetchAll();

            echo json_encode(['success' => true, 'users' => $users]);
            break;

        case 'POST':
            // Добавление нового пользователя
            $username = sanitize($_POST['username'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = sanitize($_POST['role'] ?? 'user');

            // Валидация
            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Логин и пароль обязательны']);
                exit();
            }

            // Проверка существования пользователя
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->execute([$username]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Пользователь с таким логином уже существует']);
                exit();
            }

            // Хеширование пароля
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Добавление пользователя
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $role]);

            echo json_encode(['success' => true, 'message' => 'Пользователь успешно добавлен']);
            break;

        case 'DELETE':
            // Удаление пользователя
            $userId = (int)($_GET['id'] ?? 0);

            if ($userId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Неверный ID пользователя']);
                exit();
            }

            // Проверка, что пользователь не удаляет сам себя
            if ($userId == $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Нельзя удалить самого себя']);
                exit();
            }

            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            echo json_encode(['success' => true, 'message' => 'Пользователь успешно удален']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    }

} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
} catch (Exception $e) {
    error_log("Общая ошибка: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>