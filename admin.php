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
                        <i class="icon-dashboard"></i> Дашборд
                    </li>
                    <li data-section="users">
                        <i class="icon-users"></i> Пользователи
                    </li>
                    <li data-section="content">
                        <i class="icon-content"></i> Контент
                    </li>
                    <li data-section="settings">
                        <i class="icon-settings"></i> Настройки
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="#" id="logout-btn" class="logout-btn">
                    <i class="icon-logout"></i> Выход
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

                <div class="activity-section">
                    <h2>Недавняя активность</h2>
                    <div id="activity-list" class="activity-list">
                        <p>Загрузка активности...</p>
                    </div>
                </div>
            </section>

            <!-- Пользователи -->
            <section id="users-section" class="content-section">
                <div class="section-header">
                    <h1>Управление пользователями</h1>
                    <button id="add-user-btn" class="btn-primary">Добавить пользователя</button>
                </div>

                <div class="table-container">
                    <table id="users-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Логин</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                                <th>Последний вход</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7">Загрузка пользователей...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Контент -->
            <section id="content-section" class="content-section">
                <div class="section-header">
                    <h1>Редактирование контента</h1>
                </div>

                <div class="content-editor">
                    <div class="tabs">
                        <button class="tab-btn active" data-tab="main">Главная</button>
                        <button class="tab-btn" data-tab="about">О продукте</button>
                        <button class="tab-btn" data-tab="contact">Контакты</button>
                    </div>

                    <div class="tab-content active" data-tab="main">
                        <h3>Основной контент главной страницы</h3>
                        <textarea id="main-content-editor" rows="10" placeholder="Введите основной контент главной страницы..."></textarea>
                    </div>

                    <div class="tab-content" data-tab="about">
                        <h3>Контент раздела "О продукте"</h3>
                        <textarea id="about-content-editor" rows="10" placeholder="Введите контент раздела 'О продукте'..."></textarea>
                    </div>

                    <div class="tab-content" data-tab="contact">
                        <h3>Контактная информация</h3>
                        <textarea id="contact-content-editor" rows="10" placeholder="Введите контактную информацию..."></textarea>
                    </div>

                    <button id="save-content-btn" class="btn-primary">Сохранить изменения</button>
                </div>
            </section>

            <!-- Настройки -->
            <section id="settings-section" class="content-section">
                <div class="section-header">
                    <h1>Настройки системы</h1>
                </div>

                <div class="settings-content">
                    <p>Настройки системы находятся в разработке.</p>
                </div>
            </section>
        </main>
    </div>

    <!-- Модальное окно для добавления пользователя -->
    <div id="user-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить пользователя</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="user-form">
                    <div class="form-group">
                        <label for="user-username">Логин:</label>
                        <input type="text" id="user-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email:</label>
                        <input type="email" id="user-email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="user-password">Пароль:</label>
                        <input type="password" id="user-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="user-role">Роль:</label>
                        <select id="user-role" name="role" required>
                            <option value="user">Пользователь</option>
                            <option value="admin">Администратор</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancel-user-btn">Отмена</button>
                <button class="btn-primary" id="save-user-btn">Сохранить</button>
            </div>
        </div>
    </div>

    <script src="js/admin.js?v=20241201"></script>
</body>
</html>