<?php
// Скрипт проверки авторизации

require_once 'config.php';

// Запуск сессии
session_start();

// Проверка истечения сессии
if (isset($_SESSION['expire']) && time() > $_SESSION['expire']) {
    // Сессия истекла, выход
    session_destroy();
    redirect('../login.html');
}

// Проверка авторизации
if (!isLoggedIn()) {
    redirect('../login.html');
}

// Проверка роли администратора (если требуется)
if (basename($_SERVER['PHP_SELF']) === 'admin.php' && !isAdmin()) {
    http_response_code(403);
    die('Доступ запрещен. Требуются права администратора.');
}

// Продление сессии при активности
$_SESSION['expire'] = time() + (24 * 60 * 60);
?>