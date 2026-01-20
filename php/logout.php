<?php
// Скрипт выхода из системы

require_once 'config.php';

// Запуск сессии
session_start();

// Очистка всех данных сессии
$_SESSION = array();

// Уничтожение сессии
session_destroy();

// Удаление куки сессии
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Перенаправление на страницу авторизации
redirect('/login');
?>