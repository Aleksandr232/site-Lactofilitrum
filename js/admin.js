// JavaScript для админской панели

// Функции для работы с подкастами (определены глобально)
function setupPodcastModal() {
    const modal = document.getElementById('podcast-modal');
    if (!modal) return; // Если модального окна нет на странице, выходим

    const addBtn = document.getElementById('add-podcast-btn');
    const closeBtn = document.querySelector('#podcast-modal .modal-close');
    const cancelBtn = document.getElementById('cancel-podcast-btn');
    const saveBtn = document.getElementById('save-podcast-btn');

    // Открытие модального окна
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            modal.style.display = 'block';
            const form = document.getElementById('podcast-form');
            if (form) form.reset();
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

function loadPodcasts() {
    const tableBody = document.querySelector('#podcasts-table tbody');
    if (!tableBody) return; // Если таблицы нет на странице, выходим

    fetch('php/api/podcasts.php')
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';

            if (data.success && data.podcasts && data.podcasts.length > 0) {
                data.podcasts.forEach(podcast => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${podcast.id}</td>
                        <td>${podcast.title}</td>
                        <td>${podcast.author || '-'}</td>
                        <td>${new Date(podcast.created_at).toLocaleDateString('ru-RU')}</td>
                        <td>
                            <button class="btn-danger" onclick="deletePodcast(${podcast.id})">Удалить</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5">Нет подкастов</td></tr>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки подкастов:', error);
            tableBody.innerHTML = '<tr><td colspan="5" style="color: red;">Ошибка загрузки</td></tr>';
        });
}

function savePodcast() {
    const formData = new FormData();
    formData.append('title', document.getElementById('podcast-title').value);
    formData.append('description', document.getElementById('podcast-description').value);
    formData.append('image', document.getElementById('podcast-image').value);
    formData.append('author', document.getElementById('podcast-author').value);
    formData.append('author_photo', document.getElementById('podcast-author-photo').value);
    formData.append('button_link', document.getElementById('podcast-button-link').value);
    formData.append('additional_link', document.getElementById('podcast-additional-link').value);

    fetch('php/api/podcasts.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Подкаст успешно добавлен');
            const modal = document.getElementById('podcast-modal');
            if (modal) modal.style.display = 'none';
            loadPodcasts(); // Перезагружаем список
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения подкаста:', error);
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
