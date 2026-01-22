<?php
// Страница управления подкастами
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
    <title>Подкасты - Lactofilitrum</title>
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
                    <li class="active" data-section="podcasts">
                        <i class='bx bx-podcast'></i> Подкасты
                    </li>
                    <li data-href="/remission">
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
            <!-- Подкасты -->
            <section id="podcasts-section" class="content-section active">
                <div class="section-header">
                    <h1>Подкасты с экспертами</h1>
                    <button id="add-podcast-btn" class="btn-primary">Добавить подкаст</button>
                </div>

                <div class="table-container">
                    <table id="podcasts-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Картинка</th>
                                <th>Название</th>
                                <th>Автор</th>
                                <th>Дата создания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6">Загрузка подкастов...</td>
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
                <form id="podcast-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="podcast-title">Название подкаста:</label>
                        <input type="text" id="podcast-title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="podcast-description">Описание подкаста:</label>
                        <textarea id="podcast-description" name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="podcast-image">Картинка подкаста:</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="podcast-image" name="image" accept="image/*" class="file-upload-input">
                            <div class="file-upload-label" for="podcast-image">
                                <div>
                                    <i class='bx bx-cloud-upload file-upload-icon'></i>
                                    <div class="file-upload-text">Выберите файл или перетащите сюда</div>
                                    <div class="file-upload-subtext">PNG, JPG, GIF до 10MB</div>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="podcast-image-preview">
                                <div class="file-preview-info">
                                    <i class='bx bx-file file-preview-icon'></i>
                                    <div class="file-preview-details">
                                        <div class="file-preview-name"></div>
                                        <div class="file-preview-size"></div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" onclick="console.log('Remove button clicked'); removeFile('podcast-image')">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                        <div class="upload-progress" id="podcast-image-progress">
                            <div class="loading-spinner"></div>
                            <span class="loading-text">Загрузка файла...</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="podcast-author">Автор подкаста:</label>
                        <input type="text" id="podcast-author" name="author">
                    </div>
                    <div class="form-group">
                        <label for="podcast-author-photo">Фото автора подкаста:</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="podcast-author-photo" name="author_photo" accept="image/*" class="file-upload-input">
                            <div class="file-upload-label" for="podcast-author-photo">
                                <div>
                                    <i class='bx bx-cloud-upload file-upload-icon'></i>
                                    <div class="file-upload-text">Выберите файл или перетащите сюда</div>
                                    <div class="file-upload-subtext">PNG, JPG, GIF до 10MB</div>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="podcast-author-photo-preview">
                                <div class="file-preview-info">
                                    <i class='bx bx-file file-preview-icon'></i>
                                    <div class="file-preview-details">
                                        <div class="file-preview-name"></div>
                                        <div class="file-preview-size"></div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" onclick="console.log('Remove button clicked'); removeFile('podcast-author-photo')">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                        <div class="upload-progress" id="podcast-author-photo-progress">
                            <div class="loading-spinner"></div>
                            <span class="loading-text">Загрузка файла...</span>
                        </div>
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

    <script>
        // Глобальные функции для совместимости (определяем до загрузки admin.js)
        window.showUploadProgress = function(inputId, show) {
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
        };

        window.removeFile = function(inputId) {
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
        };
    </script>
    <script src="js/admin.js?v=20241210"></script>
    <script>
        // Функции для работы с загрузкой файлов
        function setupFileUpload(inputId) {
            const input = document.getElementById(inputId);
            const wrapper = input.closest('.file-upload-wrapper');
            const label = wrapper.querySelector('.file-upload-label');
            const preview = wrapper.querySelector('.file-upload-preview');
            const progress = wrapper.querySelector('.upload-progress');

            // Обработка выбора файла
            input.addEventListener('change', function(e) {
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
            if (!file) return;

            const wrapper = document.getElementById(inputId).closest('.file-upload-wrapper');
            const label = wrapper.querySelector('.file-upload-label');
            const preview = wrapper.querySelector('.file-upload-preview');
            const progress = wrapper.querySelector('.upload-progress');

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

            previewName.textContent = file.name;
            previewSize.textContent = formatFileSize(file.size);

            label.style.display = 'none';
            preview.classList.add('show');
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


        // Специфичный код для страницы подкастов
        function initPodcastsPage() {
            console.log('initPodcastsPage called');
            console.log('setupPodcastModal available:', typeof setupPodcastModal);
            console.log('loadPodcasts available:', typeof loadPodcasts);

            // Инициализация только для подкастов
            if (typeof setupPodcastModal === 'function') {
                console.log('Calling setupPodcastModal');
                setupPodcastModal();
            } else {
                console.error('setupPodcastModal function not found');
            }

            setupFileUpload('podcast-image');
            setupFileUpload('podcast-author-photo');

            if (typeof loadPodcasts === 'function') {
                console.log('Calling loadPodcasts');
                loadPodcasts();
            } else {
                console.error('loadPodcasts function not found');
            }

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

        // Запуск инициализации после загрузки всех ресурсов
        window.addEventListener('load', function() {
            // Ждем небольшую задержку, чтобы все скрипты точно загрузились
            setTimeout(initPodcastsPage, 100);
        });
    </script>
</body>
</html>