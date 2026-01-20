<?php
// Проверка авторизации для доступа к админке
require_once 'php/auth_check.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - Lactofilitrum</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Боковое меню -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Админка</h2>
                <span class="user-info">Привет, <span id="username">Админ</span></span>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="active" data-section="dashboard">
                        <i class="icon-dashboard">📈</i> Дашборд
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="#" id="logout-btn" class="logout-btn">
                    <i class="icon-logout">🔓</i> Выход
                </a>
            </div>
        </aside>

        <!-- Основная область -->
        <main class="main-content">
            <!-- Дашборд -->
            <section id="dashboard-section" class="content-section active">
                <div class="section-header">
                    <h1>Дашборд</h1>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Всего пользователей</h3>
                        <div class="stat-number" id="total-users">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Активных сессий</h3>
                        <div class="stat-number" id="active-sessions">0</div>
                    </div>
                    <div class="stat-card">
                        <h3>Последний вход</h3>
                        <div class="stat-time" id="last-login">-</div>
                    </div>
                </div>

                <div class="welcome-section">
                    <h2>Добро пожаловать в админ-панель!</h2>
                    <p>Здесь вы можете отслеживать основную статистику системы.</p>
                </div>
            </section>
        </main>
    </div>

    <script src="js/admin.js?v=20241202"></script>
</body>
</html>