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
    <link rel="stylesheet" href="css/admin.css?v=20241203">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                        <i class='bx bx-bar-chart'></i> Дашборд
                    </li>
                    <li data-section="podcasts">
                        <i class='bx bx-podcast'></i> Подкасты
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="#" id="logout-btn" class="logout-btn">
                    <i class='bx bx-log-out'></i> Выход
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
                        <h3>Всего подкастов</h3>
                        <div class="stat-number" id="total-podcasts">0</div>
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

                <!-- Последние подкасты -->
                <div class="recent-podcasts">
                    <h2>Недавние подкасты</h2>
                    <div id="recent-podcasts-list" class="podcasts-list">
                        <p>Загрузка подкастов...</p>
                    </div>
                    <div class="podcasts-actions">
                        <a href="#" onclick="showSection('podcasts')" class="btn-primary">Управление подкастами</a>
                    </div>
                </div>
            </section>

            <!-- Подкасты -->
            <section id="podcasts-section" class="content-section">
                <div class="section-header">
                    <h1>Подкасты с экспертами</h1>
                    <button id="add-podcast-btn" class="btn-primary">Добавить подкаст</button>
                </div>

                <div class="table-container">
                    <table id="podcasts-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Автор</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5">Загрузка подкастов...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Модальное окно для добавления подкаста -->
    <div id="podcast-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить подкаст</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="podcast-form">
                    <div class="form-group">
                        <label for="podcast-title">Название подкаста:</label>
                        <input type="text" id="podcast-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="podcast-description">Описание подкаста:</label>
                        <textarea id="podcast-description" name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="podcast-image">Картинка подкаста (URL):</label>
                        <input type="url" id="podcast-image" name="image">
                    </div>
                    <div class="form-group">
                        <label for="podcast-author">Автор подкаста:</label>
                        <input type="text" id="podcast-author" name="author">
                    </div>
                    <div class="form-group">
                        <label for="podcast-author-photo">Фото автора подкаста (URL):</label>
                        <input type="url" id="podcast-author-photo" name="author_photo">
                    </div>
                    <div class="form-group">
                        <label for="podcast-button-link">Кнопка подкаста (ссылка):</label>
                        <input type="url" id="podcast-button-link" name="button_link">
                    </div>
                    <div class="form-group">
                        <label for="podcast-additional-link">Доп. ссылка подкаста:</label>
                        <input type="url" id="podcast-additional-link" name="additional_link">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancel-podcast-btn">Отмена</button>
                <button class="btn-primary" id="save-podcast-btn">Сохранить</button>
            </div>
        </div>
    </div>

    <script src="js/admin.js?v=20241203"></script>
</body>
</html>