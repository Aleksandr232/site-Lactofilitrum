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
    <link rel="stylesheet" href="css/admin.css?v=20241205">
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
                    <li data-href="/podcasts">
                        <i class='bx bx-podcast'></i> Подкасты
                    </li>
                    <li data-section="remission">
                        <i class='bx bx-book'></i> Библиотека Ремиссии
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
                        <h3>Библиотека Ремиссии</h3>
                        <div class="stat-number" id="total-remission">0</div>
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

            <!-- Библиотека Ремиссии -->
            <section id="remission-section" class="content-section">
                <div class="section-header">
                    <h1>Библиотека Ремиссии</h1>
                    <button id="add-remission-btn" class="btn-primary">Добавить элемент</button>
                </div>

                <div class="table-container">
                    <table id="remission-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Картинка</th>
                                <th>Название</th>
                                <th>Описание</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6">Загрузка элементов...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </main>
    </div>


    <!-- Модальное окно для добавления элемента библиотеки ремиссии -->
    <div id="remission-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Добавить элемент библиотеки</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="remission-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="remission-title">Название:</label>
                        <input type="text" id="remission-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="remission-description">Описание:</label>
                        <textarea id="remission-description" name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="remission-image">Картинка:</label>
                        <input type="file" id="remission-image" name="image" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" id="cancel-remission-btn">Отмена</button>
                <button class="btn-primary" id="save-remission-btn">Сохранить</button>
            </div>
        </div>
    </div>

    <script src="js/admin.js?v=20241210"></script>
</body>
</html>