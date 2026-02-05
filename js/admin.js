// JavaScript для админской панели

// Функции для работы с подкастами (определены глобально)
function setupPodcastModal() {
    console.log('setupPodcastModal called');
    const modal = document.getElementById('podcast-modal');
    console.log('Modal found:', !!modal);
    if (!modal) return; // Если модального окна нет на странице, выходим

    const addBtn = document.getElementById('add-podcast-btn');
    console.log('Add button found:', !!addBtn);
    const closeBtn = document.querySelector('#podcast-modal .modal-close');
    const cancelBtn = document.getElementById('cancel-podcast-btn');
    const saveBtn = document.getElementById('save-podcast-btn');

    // Открытие модального окна
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            document.getElementById('podcast-modal-title').textContent = 'Добавить подкаст';
            const editIdInput = document.getElementById('podcast-edit-id');
            if (editIdInput) editIdInput.value = '';
            modal.style.display = 'block';
            const form = document.getElementById('podcast-form');
            if (form) {
                form.reset();
                const imgInput = document.getElementById('podcast-image');
                const authorInput = document.getElementById('podcast-author-photo');
                const videoInput = document.getElementById('podcast-video');
                const audioInput = document.getElementById('podcast-audio');
                if (imgInput) imgInput.value = '';
                if (authorInput) authorInput.value = '';
                if (videoInput) videoInput.value = '';
                if (audioInput) audioInput.value = '';
                if (window.removeFile) {
                    if (imgInput) window.removeFile('podcast-image');
                    if (authorInput) window.removeFile('podcast-author-photo');
                    if (videoInput) window.removeFile('podcast-video');
                    if (audioInput) window.removeFile('podcast-audio');
                }
                if (typeof tinymce !== 'undefined') {
                    const editor = tinymce.get('podcast-text');
                    if (editor) editor.setContent('');
                }
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

    // Сохранение подкаста
    if (saveBtn) {
        saveBtn.addEventListener('click', savePodcast);
    }

    // Закрытие по клику вне модального окна
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function openEditPodcastModal(podcast) {
    const modal = document.getElementById('podcast-modal');
    const form = document.getElementById('podcast-form');
    const modalTitle = document.getElementById('podcast-modal-title');
    if (!modal || !form || !modalTitle) return;

    modalTitle.textContent = 'Редактировать подкаст';

    const editIdInput = document.getElementById('podcast-edit-id');
    if (editIdInput) editIdInput.value = podcast.id;

    document.getElementById('podcast-title').value = podcast.title || '';
    document.getElementById('podcast-description').value = podcast.description || '';
    document.getElementById('podcast-author').value = podcast.author || '';
    document.getElementById('podcast-button-link').value = podcast.button_link || '';
    document.getElementById('podcast-additional-link').value = podcast.additional_link || '';
    document.getElementById('podcast-extra-link').value = podcast.extra_link || '';
    const timeSelect = document.getElementById('podcast-time-podcast');
    if (timeSelect) timeSelect.value = podcast.time_podcast || '';

    if (typeof tinymce !== 'undefined') {
        const editor = tinymce.get('podcast-text');
        if (editor) editor.setContent(podcast.podcasts_text || '');
    } else {
        const textarea = document.getElementById('podcast-text');
        if (textarea) textarea.value = podcast.podcasts_text || '';
    }

    function showCurrentFile(inputId, filepath) {
        if (!filepath) return;
        const name = filepath.split('/').pop();
        const current = document.getElementById(inputId + '-current');
        const label = document.getElementById(inputId).closest('.file-upload-wrapper').querySelector('.file-upload-label');
        const preview = document.getElementById(inputId + '-preview');
        if (current && name) {
            current.querySelector('.file-current-name').textContent = name;
            current.classList.add('show');
            label.style.display = 'none';
            if (preview) preview.classList.remove('show');
        }
    }

    ['podcast-image', 'podcast-author-photo', 'podcast-video', 'podcast-audio'].forEach(id => {
        const input = document.getElementById(id);
        const wrapper = input.closest('.file-upload-wrapper');
        const label = wrapper.querySelector('.file-upload-label');
        const preview = wrapper.querySelector('.file-upload-preview');
        const current = document.getElementById(id + '-current');
        input.value = '';
        preview.classList.remove('show');
        if (current) current.classList.remove('show');
        label.style.display = 'flex';
    });

    showCurrentFile('podcast-image', podcast.image);
    showCurrentFile('podcast-author-photo', podcast.author_photo);
    showCurrentFile('podcast-video', podcast.video_path);
    showCurrentFile('podcast-audio', podcast.audio_path);

    modal.style.display = 'block';
}

function editPodcast(podcastId) {
    fetch('php/api/podcasts.php?id=' + podcastId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.podcast) {
                openEditPodcastModal(data.podcast);
            } else {
                alert('Ошибка загрузки подкаста');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Ошибка загрузки подкаста');
        });
}

function loadPodcasts() {
    const tableBody = document.querySelector('#podcasts-table tbody');
    if (!tableBody) return; // Если таблицы нет на странице, выходим

    fetch('php/api/podcasts.php')
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';

            if (data.success && data.podcasts && data.podcasts.length > 0) {
                data.podcasts.forEach(podcast => {
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

                    const imageUrl = getImageUrl(podcast.image);
                    const imageHtml = imageUrl ?
                        `<img src="${imageUrl}" alt="${podcast.title}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">` :
                        '<span style="color: #7f8c8d;">Нет картинки</span>';

                    const authorPhotoUrl = getImageUrl(podcast.author_photo);
                    const authorHtml = podcast.author ?
                        `${podcast.author}${authorPhotoUrl ? ` <img src="${authorPhotoUrl}" alt="${podcast.author}" style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%; vertical-align: middle;">` : ''}` :
                        '-';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${podcast.id}</td>
                        <td>${imageHtml}</td>
                        <td>${podcast.title}</td>
                        <td>${authorHtml}</td>
                        <td>${new Date(podcast.created_at).toLocaleDateString('ru-RU')}</td>
                        <td>
                            <button class="btn-secondary" onclick="editPodcast(${podcast.id})" style="margin-right:6px;">Изменить</button>
                            <button class="btn-danger" onclick="deletePodcast(${podcast.id})">Удалить</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="6">Нет подкастов</td></tr>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки подкастов:', error);
            tableBody.innerHTML = '<tr><td colspan="6" style="color: red;">Ошибка загрузки</td></tr>';
        });
}

// Эти функции теперь определяются локально на каждой странице
// Глобальные версии доступны через window.showUploadProgress и window.removeFile

function savePodcast() {
    console.log('savePodcast called');

    // Показываем индикатор загрузки (используем глобальные функции)
    if (window.showUploadProgress) {
        window.showUploadProgress('podcast-image', true);
        window.showUploadProgress('podcast-author-photo', true);
        window.showUploadProgress('podcast-video', true);
        window.showUploadProgress('podcast-audio', true);
    }

    // Сохраняем содержимое TinyMCE в textarea перед отправкой
    let podcastsTextContent = '';
    
    // Сначала пытаемся получить содержимое из TinyMCE
    if (typeof tinymce !== 'undefined') {
        try {
            const editor = tinymce.get('podcast-text');
            if (editor && !editor.isHidden()) {
                // Получаем содержимое напрямую из редактора
                podcastsTextContent = editor.getContent();
                console.log('TinyMCE content retrieved, length:', podcastsTextContent.length);
                console.log('TinyMCE content preview:', podcastsTextContent.substring(0, 200));
                
                // Сохраняем содержимое редактора в textarea (синхронизация)
                editor.save();
                console.log('TinyMCE content saved to textarea');
            } else {
                console.warn('TinyMCE editor not found or hidden for podcast-text');
                // Если редактор не найден, берем значение из textarea
                const textarea = document.getElementById('podcast-text');
                if (textarea) {
                    podcastsTextContent = textarea.value;
                    console.log('Content taken from textarea (editor not available), length:', podcastsTextContent.length);
                }
            }
        } catch (e) {
            console.error('Error getting TinyMCE content:', e);
            // В случае ошибки берем значение из textarea
            const textarea = document.getElementById('podcast-text');
            if (textarea) {
                podcastsTextContent = textarea.value;
                console.log('Content taken from textarea (error occurred), length:', podcastsTextContent.length);
            }
        }
    } else {
        console.warn('TinyMCE not loaded');
        // Если TinyMCE не загружен, берем значение из textarea
        const textarea = document.getElementById('podcast-text');
        if (textarea) {
            podcastsTextContent = textarea.value;
            console.log('Content taken from textarea (TinyMCE not loaded), length:', podcastsTextContent.length);
        }
    }
    
    // Также обновляем textarea напрямую на случай, если save() не сработал
    const textarea = document.getElementById('podcast-text');
    if (textarea && podcastsTextContent !== textarea.value) {
        textarea.value = podcastsTextContent;
        console.log('Textarea updated directly with content, length:', podcastsTextContent.length);
    }

    const form = document.getElementById('podcast-form');
    const formData = new FormData(form);
    formData.set('podcasts_text', podcastsTextContent);

    const editId = document.getElementById('podcast-edit-id');
    const isEdit = editId && editId.value;
    if (isEdit) formData.set('id', editId.value);
    console.log('podcasts_text set in FormData, length:', podcastsTextContent.length);

    console.log('Form element found:', !!form);
    console.log('Form has enctype:', form.getAttribute('enctype'));

    // Проверяем, что файлы добавлены
    const imageInput = document.getElementById('podcast-image');
    const authorPhotoInput = document.getElementById('podcast-author-photo');

    console.log('Image input files:', imageInput.files.length);
    console.log('Author photo input files:', authorPhotoInput.files.length);

    // Логируем содержимое FormData
    console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
        if (value instanceof File) {
            console.log(key, '(File):', value.name, 'size:', value.size);
        } else {
            console.log(key, '(Text):', value);
        }
    }

    fetch('php/api/podcasts.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Скрываем индикатор загрузки
        showUploadProgress('podcast-image', false);
        showUploadProgress('podcast-author-photo', false);
        showUploadProgress('podcast-video', false);
        showUploadProgress('podcast-audio', false);

        if (data.success) {
            alert(isEdit ? 'Подкаст успешно обновлён' : 'Подкаст успешно добавлен');
            const modal = document.getElementById('podcast-modal');
            if (modal) modal.style.display = 'none';
            if (form) form.reset();
            const editIdEl = document.getElementById('podcast-edit-id');
            if (editIdEl) editIdEl.value = '';
            if (window.removeFile) {
                window.removeFile('podcast-image');
                window.removeFile('podcast-author-photo');
                window.removeFile('podcast-video');
                window.removeFile('podcast-audio');
            }
            if (typeof tinymce !== 'undefined') {
                const editor = tinymce.get('podcast-text');
                if (editor) editor.setContent('');
            }
            loadPodcasts();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения подкаста:', error);
        // Скрываем индикатор загрузки
        if (window.showUploadProgress) {
            window.showUploadProgress('podcast-image', false);
            window.showUploadProgress('podcast-author-photo', false);
            window.showUploadProgress('podcast-video', false);
            window.showUploadProgress('podcast-audio', false);
        }
        alert('Ошибка сохранения подкаста');
    });
}

function deletePodcast(podcastId) {
    if (confirm('Вы уверены, что хотите удалить этот подкаст?')) {
        fetch(`php/api/podcasts.php?id=${podcastId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadPodcasts(); // Перезагружаем список после удаления
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка удаления подкаста:', error);
            alert('Ошибка удаления подкаста');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Проверка авторизации при загрузке страницы
    checkAuthStatus();

    // Обработчики для бокового меню
    const navItems = document.querySelectorAll('.sidebar-nav li');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const section = this.dataset.section;
            const href = this.dataset.href;

            if (href) {
                // Если есть data-href, делаем перенаправление
                window.location.href = href;
            } else if (section) {
                // Иначе показываем секцию
                showSection(section);

                // Обновление активного пункта меню
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Обработчик выхода
    document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        logout();
    });

    // Загрузка начальных данных
    loadDashboardData();
});

function checkAuthStatus() {
    fetch('php/auth_check.php')
        .then(response => {
            if (!response.ok) {
                window.location.href = '/login';
            }
        })
        .catch(error => {
            console.error('Ошибка проверки авторизации:', error);
            window.location.href = '/login';
        });
}

function showSection(sectionName) {
    // Скрываем все секции
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => section.classList.remove('active'));

    // Показываем выбранную секцию
    const targetSection = document.getElementById(sectionName + '-section');
    if (targetSection) {
        targetSection.classList.add('active');
    }
}

function logout() {
    fetch('php/logout.php')
        .then(() => {
            window.location.href = '/login';
        })
        .catch(error => {
            console.error('Ошибка выхода:', error);
            window.location.href = '/login';
        });
}

function loadDashboardData() {
    // Загрузка статистики пользователей
    fetch('php/api/dashboard.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.totalUsers || 0;
            document.getElementById('active-sessions').textContent = data.activeSessions || 0;
            document.getElementById('last-login').textContent = data.lastLogin || '-';
        })
        .catch(error => {
            console.error('Ошибка загрузки данных дашборда:', error);
        });

    // Загрузка статистики подкастов
    fetch('php/api/podcasts.php')
        .then(response => response.json())
        .then(data => {
            const totalPodcasts = data.success && data.podcasts ? data.podcasts.length : 0;
            document.getElementById('total-podcasts').textContent = totalPodcasts;
        })
        .catch(error => {
            console.error('Ошибка загрузки статистики подкастов:', error);
            document.getElementById('total-podcasts').textContent = '0';
        });

}

// Функции для работы с библиотекой ремиссии
function setupRemissionModal() {
    const modal = document.getElementById('remission-modal');
    if (!modal) return; // Если модального окна нет на странице, выходим

    const addBtn = document.getElementById('add-remission-btn');
    const closeBtn = document.querySelector('#remission-modal .modal-close');
    const cancelBtn = document.getElementById('cancel-remission-btn');
    const saveBtn = document.getElementById('save-remission-btn');

    // Открытие модального окна
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            console.log('Add button clicked for modal');
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
    if (!tableBody) return; // Если таблицы нет на странице, выходим

    fetch('php/api/remission.php')
        .then(response => response.json())
        .then(data => {
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
