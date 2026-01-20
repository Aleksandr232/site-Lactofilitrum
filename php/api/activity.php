<?php
// API для получения активности системы

require_once '../config.php';
require_once '../auth_check.php';

// Установка заголовков
header('Content-Type: application/json');

try {
    $conn = connectDB();

    // Получение последних 10 попыток входа
    $stmt = $conn->prepare("
        SELECT
            username,
            success,
            login_time,
            ip_address
        FROM login_logs
        ORDER BY login_time DESC
        LIMIT 10
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll();

    $activities = [];
    foreach ($logs as $log) {
        $description = $log['success']
            ? "Успешный вход пользователя {$log['username']}"
            : "Неудачная попытка входа пользователя {$log['username']}";

        $activities[] = [
            'description' => $description,
            'time' => date('d.m.Y H:i', strtotime($log['login_time'])),
            'ip' => $log['ip_address']
        ];
    }

    echo json_encode([
        'success' => true,
        'activities' => $activities
    ]);

} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
} catch (Exception $e) {
    error_log("Общая ошибка: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Неизвестная ошибка']);
}
?>