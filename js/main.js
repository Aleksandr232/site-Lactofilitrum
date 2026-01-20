// Основной JavaScript файл для сайта

document.addEventListener('DOMContentLoaded', function() {
    console.log('Сайт Lactofilitrum загружен');

    // Плавная прокрутка к секциям
    const navLinks = document.querySelectorAll('nav a[href^="#"]');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);

            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Добавляем эффект при наведении на элементы списка преимуществ
    const benefitItems = document.querySelectorAll('#benefits ul li');

    benefitItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s';
        });

        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});