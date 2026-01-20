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

    // Обработчики для табов редактора контента
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            showTab(tab);
        });
    });

    // Обработчики для модального окна пользователей
    setupUserModal();

    // Загрузка данных пользователей
    loadUsers();

    // Загрузка контента
    loadContent();
});

function checkAuthStatus() {
    fetch('php/auth_check.php')
        .then(response => {
            if (!response.ok) {
                window.location.href = 'login.html';
            }
        })
        .catch(error => {
            console.error('Ошибка проверки авторизации:', error);
            window.location.href = 'login.html';
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
    // Загрузка статистики
    fetch('php/api/dashboard.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.totalUsers || 0;
            document.getElementById('active-sessions').textContent = data.activeSessions || 0;
            document.getElementById('last-login').textContent = data.lastLogin || '-';

            // Загрузка активности
            loadActivity();
        })
        .catch(error => {
            console.error('Ошибка загрузки данных дашборда:', error);
        });
}

function loadActivity() {
    fetch('php/api/activity.php')
        .then(response => response.json())
        .then(data => {
            const activityList = document.getElementById('activity-list');
            activityList.innerHTML = '';

            if (data.activities && data.activities.length > 0) {
                data.activities.forEach(activity => {
                    const activityItem = document.createElement('div');
                    activityItem.className = 'activity-item';
                    activityItem.innerHTML = `
                        <div>${activity.description}</div>
                        <div class="activity-time">${activity.time}</div>
                    `;
                    activityList.appendChild(activityItem);
                });
            } else {
                activityList.innerHTML = '<p>Нет недавней активности</p>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки активности:', error);
        });
}

function setupUserModal() {
    const modal = document.getElementById('user-modal');
    const addBtn = document.getElementById('add-user-btn');
    const closeBtn = document.querySelector('.modal-close');
    const cancelBtn = document.getElementById('cancel-user-btn');
    const saveBtn = document.getElementById('save-user-btn');

    // Открытие модального окна
    addBtn.addEventListener('click', () => {
        modal.style.display = 'block';
        document.getElementById('user-form').reset();
    });

    // Закрытие модального окна
    closeBtn.addEventListener('click', () => modal.style.display = 'none');
    cancelBtn.addEventListener('click', () => modal.style.display = 'none');

    // Сохранение пользователя
    saveBtn.addEventListener('click', saveUser);

    // Закрытие по клику вне модального окна
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function saveUser() {
    const formData = new FormData();
    formData.append('username', document.getElementById('user-username').value);
    formData.append('email', document.getElementById('user-email').value);
    formData.append('password', document.getElementById('user-password').value);
    formData.append('role', document.getElementById('user-role').value);

    fetch('php/api/users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Пользователь успешно добавлен');
            document.getElementById('user-modal').style.display = 'none';
            loadUsers();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения пользователя:', error);
        alert('Ошибка сохранения пользователя');
    });
}

function loadUsers() {
    fetch('php/api/users.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#users-table tbody');
            tbody.innerHTML = '';

            if (data.users && data.users.length > 0) {
                data.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email || '-'}</td>
                        <td>${user.role === 'admin' ? 'Администратор' : 'Пользователь'}</td>
                        <td>${user.is_active ? 'Активен' : 'Заблокирован'}</td>
                        <td>${user.last_login || '-'}</td>
                        <td>
                            <button class="btn-danger" onclick="deleteUser(${user.id})">Удалить</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7">Нет пользователей</td></tr>';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки пользователей:', error);
        });
}

function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        fetch(`php/api/users.php?id=${userId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadUsers();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка удаления пользователя:', error);
        });
    }
}

function showTab(tabName) {
    // Скрываем все табы
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Убираем активный класс у всех кнопок
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => btn.classList.remove('active'));

    // Показываем выбранный таб
    const targetTab = document.querySelector(`.tab-content[data-tab="${tabName}"]`);
    const targetBtn = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);

    if (targetTab && targetBtn) {
        targetTab.classList.add('active');
        targetBtn.classList.add('active');
    }
}

function loadContent() {
    fetch('php/api/content.php')
        .then(response => response.json())
        .then(data => {
            if (data.content) {
                document.getElementById('main-content-editor').value = data.content.main || '';
                document.getElementById('about-content-editor').value = data.content.about || '';
                document.getElementById('contact-content-editor').value = data.content.contact || '';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки контента:', error);
        });
}

// Сохранение контента
document.getElementById('save-content-btn').addEventListener('click', function() {
    const formData = new FormData();
    formData.append('main', document.getElementById('main-content-editor').value);
    formData.append('about', document.getElementById('about-content-editor').value);
    formData.append('contact', document.getElementById('contact-content-editor').value);

    fetch('php/api/content.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Контент успешно сохранен');
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения контента:', error);
    });
});