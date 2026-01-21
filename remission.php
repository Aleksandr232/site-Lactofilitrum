<?php
// Страница библиотеки ремиссии
require_once 'php/config.php';

// Инициализируем базу данных, если нужно
initializeDatabase();

require_once 'php/auth_check.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Библиотека Ремиссии - Lactofilitrum</title>
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
                    <li data-href="/admin">
                        <i class='bx bx-bar-chart'></i> Дашборд
                    </li>
                    <li data-href="/podcasts">
                        <i class='bx bx-podcast'></i> Подкасты
                    </li>
                    <li class="active" data-section="remission">
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
            <!-- Библиотека Ремиссии -->
            <section id="remission-section" class="content-section active">
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
    <script>
        // Функции для работы с библиотекой ремиссии (локальные для этой страницы)
        function setupRemissionModal() {
            const modal = document.getElementById('remission-modal');
            if (!modal) {
                console.error('Modal remission-modal not found');
                return;
            }

            const addBtn = document.getElementById('add-remission-btn');
            const closeBtn = document.querySelector('#remission-modal .modal-close');
            const cancelBtn = document.getElementById('cancel-remission-btn');
            const saveBtn = document.getElementById('save-remission-btn');

            console.log('Setting up remission modal, addBtn:', !!addBtn);

            // Открытие модального окна
            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    console.log('Add remission button clicked');
                    modal.style.display = 'block';
                    const form = document.getElementById('remission-form');
                    if (form) {
                        form.reset();
                        // Очищаем значения файловых input
                        document.getElementById('remission-image').value = '';
                    }
                });
            }

            // Закрытие модального окна
            if (closeBtn) {
                closeBtn.addEventListener('click', () => modal.style.display = 'none');
            }
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => modal.style.display = 'none');
            }

            // Сохранение элемента
            if (saveBtn) {
                saveBtn.addEventListener('click', saveRemission);
            }

            // Закрытие по клику вне модального окна
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        function loadRemission() {
            const tableBody = document.querySelector('#remission-table tbody');
            if (!tableBody) {
                console.error('Remission table body not found');
                return;
            }

            console.log('Loading remission items...');

            fetch('php/api/remission.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Remission data received:', data);
                    tableBody.innerHTML = '';

                    if (data.success && data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            // Функция для получения полного URL изображения
                            const getImageUrl = (imagePath) => {
                                if (!imagePath) return null;
                                // Если путь уже содержит http/https, возвращаем как есть
                                if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                                    return imagePath;
                                }
                                // Иначе добавляем базовый URL
                                return window.location.origin + '/' + imagePath;
                            };

                            const imageUrl = getImageUrl(item.image);
                            const imageHtml = imageUrl ?
                                `<img src="${imageUrl}" alt="${item.title}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">` :
                                '<span style="color: #7f8c8d;">Нет картинки</span>';

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.id}</td>
                                <td>${imageHtml}</td>
                                <td>${item.title}</td>
                                <td>${item.description || '-'}</td>
                                <td>${new Date(item.created_at).toLocaleDateString('ru-RU')}</td>
                                <td>
                                    <button class="btn-danger" onclick="deleteRemission(${item.id})">Удалить</button>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="6">Нет элементов</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Ошибка загрузки элементов remission:', error);
                    tableBody.innerHTML = '<tr><td colspan="6" style="color: red;">Ошибка загрузки</td></tr>';
                });
        }

        function saveRemission() {
            console.log('saveRemission called');

            // Альтернативный способ: используем FormData из самой формы
            const form = document.getElementById('remission-form');
            const formData = new FormData(form);

            console.log('Form element found:', !!form);

            // Проверяем, что файлы добавлены
            const imageInput = document.getElementById('remission-image');

            console.log('Image input files:', imageInput.files.length);

            fetch('php/api/remission.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Save response:', data);
                if (data.success) {
                    alert('Элемент успешно добавлен');
                    const modal = document.getElementById('remission-modal');
                    if (modal) modal.style.display = 'none';
                    loadRemission(); // Перезагружаем список
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка сохранения элемента remission:', error);
                alert('Ошибка сохранения элемента');
            });
        }

        function deleteRemission(itemId) {
            if (confirm('Вы уверены, что хотите удалить этот элемент?')) {
                fetch(`php/api/remission.php?id=${itemId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadRemission(); // Перезагружаем список после удаления
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка удаления элемента remission:', error);
                    alert('Ошибка удаления элемента');
                });
            }
        }

        // Специфичный код для страницы библиотеки ремиссии
        function initRemissionPage() {
            console.log('initRemissionPage called');

            // Инициализация remission
            setupRemissionModal();
            loadRemission();

            // Установка имени пользователя
            fetch('php/auth_check.php')
                .then(response => response.json())
                .then(data => {
                    if (data.username) {
                        document.getElementById('username').textContent = data.username;
                    }
                })
                .catch(error => {
                    console.error('Ошибка получения данных пользователя:', error);
                });
        }

        // Запуск инициализации после загрузки страницы
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRemissionPage);
        } else {
            initRemissionPage();
        }
    </script>
</body>
</html>