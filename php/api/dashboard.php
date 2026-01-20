<?php
// API для данных дашборда

require_once '../config.php';
require_once '../auth_check.php';

// Установка заголовков
header('Content-Type: application/json');

try {
    $conn = connectDB();

    // Получение общего количества пользователей
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch()['total'];

    // Получение количества активных сессий (примерно)
    // В реальном приложении можно использовать более сложную логику
    $activeSessions = 1; // Пока заглушка

    // Получение времени последнего входа текущего пользователя
    $stmt = $conn->prepare("SELECT last_login FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $lastLogin = $stmt->fetch()['last_login'];

    // Форматирование времени последнего входа
    $formattedLastLogin = $lastLogin ? date('d.m.Y H:i', strtotime($lastLogin)) : 'Никогда';

    echo json_encode([
        'success' => true,
        'totalUsers' => $totalUsers,
        'activeSessions' => $activeSessions,
        'lastLogin' => $formattedLastLogin
    ]);

} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
} catch (Exception $e) {
    error_log("Общая ошибка: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>