// JavaScript для админской панели

document.addEventListener('DOMContentLoaded', function() {
    // Проверка авторизации при загрузке страницы
    checkAuthStatus();

    // Обработчики для бокового меню
    const navItems = document.querySelectorAll('.sidebar-nav li');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const section = this.dataset.section;
            showSection(section);

            // Обновление активного пункта меню
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Обработчик выхода
    document.getElementById('logout-btn').addEventListener('click', function(e) {
        e.preventDefault();
        logout();
    });

    // Загрузка начальных данных
    loadDashboardData();

    // Инициализация модальных окон
    setupPodcastModal();
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

        // Загружаем данные для соответствующего раздела
        if (sectionName === 'podcasts') {
            loadPodcasts();
        }
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
    // Загрузка статистики
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
}

// Функции для работы с подкастами
function loadPodcasts() {
    fetch('php/api/podcasts.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#podcasts-table tbody');
            tbody.innerHTML = '';

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
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5">Нет подкастов</td></tr>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки подкастов:', error);
        });
}

function setupPodcastModal() {
    const modal = document.getElementById('podcast-modal');
    const addBtn = document.getElementById('add-podcast-btn');
    const closeBtn = document.querySelector('#podcast-modal .modal-close');
    const cancelBtn = document.getElementById('cancel-podcast-btn');
    const saveBtn = document.getElementById('save-podcast-btn');

    // Открытие модального окна
    addBtn.addEventListener('click', () => {
        modal.style.display = 'block';
        document.getElementById('podcast-form').reset();
    });

    // Закрытие модального окна
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    // Сохранение подкаста
    saveBtn.addEventListener('click', savePodcast);

    // Закрытие по клику вне модального окна
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
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
            document.getElementById('podcast-modal').style.display = 'none';
            loadPodcasts();
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
                loadPodcasts();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка удаления подкаста:', error);
        });
    }
}

// Инициализация модального окна для подкастов
setupPodcastModal();