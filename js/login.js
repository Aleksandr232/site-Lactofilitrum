// JavaScript для страницы авторизации

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Очистка предыдущих сообщений
        hideMessage();

        // Валидация
        if (!username || !password) {
            showMessage('Пожалуйста, заполните все поля', 'error');
            return;
        }

        // Показываем индикатор загрузки
        const submitBtn = document.querySelector('.login-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Вход...';
        submitBtn.disabled = true;

        // Отправка данных на сервер
        fetch('php/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Вход выполнен успешно! Перенаправление...', 'success');
                setTimeout(() => {
                    window.location.href = '/admin';
                }, 1500);
            } else {
                showMessage(data.message || 'Ошибка авторизации', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showMessage('Ошибка соединения с сервером', 'error');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = `message ${type}`;
        messageDiv.style.display = 'block';
    }

    function hideMessage() {
        messageDiv.style.display = 'none';
        messageDiv.className = 'message';
    }

    // Автофокус на поле логина
    document.getElementById('username').focus();
});