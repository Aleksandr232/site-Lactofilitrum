<?php
// Страница библиотеки ремиссии
require_once 'php/config.php';

// Инициализируем базу данных, если нужно
initializeDatabase();

require_once 'php/auth_check.php';

// Генерируем timestamp для предотвращения кеширования
$timestamp = time();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Библиотека Ремиссии - Lactofilitrum</title>
    <link rel="stylesheet" href="css/admin.css?v=<?php echo $timestamp; ?>">
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
                        <div class="file-upload-wrapper">
                            <input type="file" id="remission-image" name="image" accept="image/*" class="file-upload-input">
                            <div class="file-upload-label" for="remission-image">
                                <div>
                                    <i class='bx bx-cloud-upload file-upload-icon'></i>
                                    <div class="file-upload-text">Выберите файл или перетащите сюда</div>
                                    <div class="file-upload-subtext">PNG, JPG, GIF до 10MB</div>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="remission-image-preview">
                                <div class="file-preview-info">
                                    <i class='bx bx-file file-preview-icon'></i>
                                    <div class="file-preview-details">
                                        <div class="file-preview-name"></div>
                                        <div class="file-preview-size"></div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" onclick="console.log('Remove button clicked'); removeFile('remission-image')">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                        <div class="upload-progress" id="remission-image-progress">
                            <div class="loading-spinner"></div>
                            <span class="loading-text">Загрузка файла...</span>
                        </div>
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
                saveBtn.addEventListener('click', () => {
                    console.log('Save button clicked');
                    saveRemission();
                });
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

            // Показываем индикатор загрузки
            showUploadProgress('remission-image', true);

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

                // Скрываем индикатор загрузки
                showUploadProgress('remission-image', false);

                if (data.success) {
                    alert('Элемент успешно добавлен');
                    const modal = document.getElementById('remission-modal');
                    if (modal) modal.style.display = 'none';
                    // Очищаем форму
                    removeFile('remission-image');
                    loadRemission(); // Перезагружаем список
                } else {
                    alert('Ошибка: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка сохранения элемента remission:', error);
                // Скрываем индикатор загрузки
                showUploadProgress('remission-image', false);
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

        // Функции для работы с загрузкой файлов
        function setupFileUpload(inputId) {
            console.log('Setting up file upload for:', inputId);
            const input = document.getElementById(inputId);
            const wrapper = input.closest('.file-upload-wrapper');
            const label = wrapper.querySelector('.file-upload-label');
            const preview = wrapper.querySelector('.file-upload-preview');
            const progress = wrapper.querySelector('.upload-progress');

            console.log('Elements found:', { input: !!input, wrapper: !!wrapper, label: !!label, preview: !!preview, progress: !!progress });

            // Обработка выбора файла
            input.addEventListener('change', function(e) {
                console.log('File input changed for:', inputId);
                handleFileSelect(e.target.files[0], inputId);
            });

            // Drag and drop
            label.addEventListener('dragover', function(e) {
                e.preventDefault();
                label.classList.add('dragover');
            });

            label.addEventListener('dragleave', function(e) {
                e.preventDefault();
                label.classList.remove('dragover');
            });

            label.addEventListener('drop', function(e) {
                e.preventDefault();
                label.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleFileSelect(files[0], inputId);
                }
            });
        }

        function handleFileSelect(file, inputId) {
            console.log('handleFileSelect called for:', inputId, 'file:', file);
            if (!file) return;

            const wrapper = document.getElementById(inputId).closest('.file-upload-wrapper');
            const label = wrapper.querySelector('.file-upload-label');
            const preview = wrapper.querySelector('.file-upload-preview');
            const progress = wrapper.querySelector('.upload-progress');

            console.log('Wrapper elements:', { wrapper: !!wrapper, label: !!label, preview: !!preview, progress: !!progress });

            // Проверяем размер файла (10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('Файл слишком большой. Максимальный размер: 10MB');
                return;
            }

            // Проверяем тип файла
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Неверный тип файла. Разрешены только изображения: JPEG, PNG, GIF');
                return;
            }

            // Показываем превью
            const previewName = preview.querySelector('.file-preview-name');
            const previewSize = preview.querySelector('.file-preview-size');

            console.log('Preview elements:', { previewName: !!previewName, previewSize: !!previewSize });

            previewName.textContent = file.name;
            previewSize.textContent = formatFileSize(file.size);

            console.log('Setting label display to none and adding show class to preview');
            label.style.display = 'none';
            preview.classList.add('show');
            console.log('Preview classList:', preview.classList);
        }

        function removeFile(inputId) {
            console.log('Removing file for:', inputId);
            const input = document.getElementById(inputId);
            const wrapper = input.closest('.file-upload-wrapper');
            const label = wrapper.querySelector('.file-upload-label');
            const preview = wrapper.querySelector('.file-upload-preview');

            console.log('Elements found:', {
                input: !!input,
                wrapper: !!wrapper,
                label: !!label,
                preview: !!preview
            });

            // Очищаем input
            input.value = '';

            // Скрываем превью, показываем label
            preview.classList.remove('show');
            label.style.display = 'flex';
            console.log('File removed successfully');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showUploadProgress(inputId, show) {
            console.log('showUploadProgress called for:', inputId, 'show:', show);
            // Элемент progress находится рядом с wrapper, а не внутри него
            const progress = document.getElementById(inputId + '-progress');

            console.log('Progress element found:', !!progress);

            if (progress) {
                if (show) {
                    progress.classList.add('show');
                    console.log('Added show class to progress');
                } else {
                    progress.classList.remove('show');
                    console.log('Removed show class from progress');
                }
            } else {
                console.error('Progress element not found for inputId:', inputId);
            }
        }

        // Глобальные функции для совместимости
        window.showUploadProgress = showUploadProgress;
        window.removeFile = removeFile;

        // Специфичный код для страницы библиотеки ремиссии
        function initRemissionPage() {
            console.log('initRemissionPage called');

            // Проверяем, что функции доступны
            console.log('Functions available:', {
                setupRemissionModal: typeof setupRemissionModal,
                setupFileUpload: typeof setupFileUpload,
                loadRemission: typeof loadRemission,
                showUploadProgress: typeof showUploadProgress,
                removeFile: typeof removeFile
            });

            // Инициализация remission
            setupRemissionModal();
            setupFileUpload('remission-image');
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