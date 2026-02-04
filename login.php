<?php
// Генерируем timestamp для предотвращения кеширования
$timestamp = time();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="frontend/img/favicon/favicon.ico?v=<?php echo $timestamp; ?>" />
		<link rel="icon" type="image/png" sizes="32x32" href="frontend/img/favicon/favicon-32x32.png?v=<?php echo $timestamp; ?>" />
		<link rel="icon" type="image/png" sizes="16x16" href="frontend/img/favicon/favicon-16x16.png?v=<?php echo $timestamp; ?>" />
		<link rel="apple-touch-icon" href="frontend/img/favicon/apple-touch-icon.png?v=<?php echo $timestamp; ?>" />
    <title>Вход в админку - Нетоксичный контент</title>
    <link rel="stylesheet" href="css/login.css?v=<?php echo $timestamp; ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Вход в админку</h2>

            <div id="message" class="message" style="display: none;"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Логин:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="login-btn">Войти</button>
            </form>

            <div class="back-link">
                <a href="/">← Вернуться на главную</a>
            </div>
        </div>
    </div>

    <script src="js/login.js?v=<?php echo $timestamp; ?>"></script>
</body>
</html>