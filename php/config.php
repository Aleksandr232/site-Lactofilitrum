<?php
// Конфигурационный файл для подключения к базе данных

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'cz19567_lacto');
define('DB_USER', 'cz19567_lacto');
define('DB_PASS', 'AhLiNBc6');

// Настройки сайта
define('SITE_NAME', 'Lactofilitrum');
// Автоматическое определение URL сайта
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('SITE_URL', $protocol . $domain);

// Настройки сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Автоматическая настройка cookie_secure для HTTPS
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 1 : 0);

// Функция для инициализации базы данных
function initializeDatabase() {
    try {
        // Подключаемся к MySQL без указания базы данных
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        // Проверяем существование базы данных
        $result = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
        $databaseExists = $result->fetch();

        if (!$databaseExists) {
            // Создаем базу данных
            $pdo->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            error_log("База данных '" . DB_NAME . "' создана автоматически");

            // Подключаемся к созданной базе данных
            $pdo->exec("USE `" . DB_NAME . "`");

            // Создаем таблицы
            createTables($pdo);
            createDefaultAdmin($pdo);
        } else {
            // База данных существует, проверяем таблицы
            $pdo->exec("USE `" . DB_NAME . "`");
            ensureTablesExist($pdo);
        }

    } catch (PDOException $e) {
        error_log("Ошибка инициализации базы данных: " . $e->getMessage());
        // Не прерываем выполнение, если база данных уже существует
    }
}

// Функция для создания таблиц
function createTables($pdo) {
    try {
        // Создаем таблицу users
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100),
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                is_active BOOLEAN DEFAULT TRUE
            )
        ");
        error_log("Таблица users создана");

        // Создаем индексы для users
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
        error_log("Индексы для users созданы");

        // Создаем таблицу login_logs
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS login_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                username VARCHAR(50),
                ip_address VARCHAR(45),
                user_agent TEXT,
                login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                success BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
        error_log("Таблица login_logs создана");

        // Создаем индексы для login_logs
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_login_time ON login_logs(login_time)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_id ON login_logs(user_id)");
        error_log("Индексы для login_logs созданы");

        // Создаем таблицу podcasts
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS podcasts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) DEFAULT NULL,
                description TEXT,
                podcasts_text TEXT,
                image VARCHAR(500),
                author VARCHAR(255),
                author_photo VARCHAR(500),
                button_link VARCHAR(500),
                additional_link VARCHAR(500),
                extra_link VARCHAR(500),
                video_path VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        error_log("Таблица podcasts создана");

        // Миграция: добавить video_path, если колонки ещё нет
        $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'video_path'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE podcasts ADD COLUMN video_path VARCHAR(500) DEFAULT NULL AFTER additional_link");
            error_log("Колонка video_path добавлена в podcasts");
        }
        // Миграция: добавить extra_link, если колонки ещё нет
        $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'extra_link'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE podcasts ADD COLUMN extra_link VARCHAR(500) DEFAULT NULL AFTER additional_link");
            error_log("Колонка extra_link добавлена в podcasts");
        }
        // Миграция: добавить slug, если колонки ещё нет
        $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'slug'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE podcasts ADD COLUMN slug VARCHAR(255) DEFAULT NULL AFTER title");
            $pdo->exec("CREATE UNIQUE INDEX idx_podcasts_slug ON podcasts(slug)");
            error_log("Колонка slug добавлена в podcasts");
        }
        // Миграция: добавить podcasts_text, если колонки ещё нет
        $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'podcasts_text'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE podcasts ADD COLUMN podcasts_text TEXT DEFAULT NULL AFTER description");
            error_log("Колонка podcasts_text добавлена в podcasts");
        }

        // Создаем индексы для podcasts
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_podcasts_title ON podcasts(title)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_podcasts_author ON podcasts(author)");
        try {
            $pdo->exec("CREATE UNIQUE INDEX idx_podcasts_slug ON podcasts(slug)");
        } catch (PDOException $e) { /* уже есть */ }
        error_log("Индексы для podcasts созданы");

        // Создаем таблицу remission_library
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS remission_library (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                image VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        error_log("Таблица remission_library создана");

        // Создаем индексы для remission_library
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_remission_title ON remission_library(title)");
        error_log("Индексы для remission_library созданы");

        // Создаем таблицу clients (клиенты регистрации)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                surname VARCHAR(100) NOT NULL,
                name VARCHAR(100) NOT NULL,
                patronymic VARCHAR(100) DEFAULT NULL,
                specialty VARCHAR(255) DEFAULT NULL,
                phone VARCHAR(50) DEFAULT NULL,
                email VARCHAR(255) NOT NULL,
                city VARCHAR(255) DEFAULT NULL,
                consent_personal TINYINT(1) DEFAULT 0,
                consent_ads TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        error_log("Таблица clients создана");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_clients_email ON clients(email)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_clients_created ON clients(created_at)");
        error_log("Индексы для clients созданы");

        error_log("Все таблицы базы данных созданы успешно");
    } catch (PDOException $e) {
        error_log("Ошибка при создании таблиц: " . $e->getMessage());
        throw $e; // Перебрасываем исключение
    }
}

// Функция для создания администратора по умолчанию
function createDefaultAdmin($pdo) {
    try {
        // Проверяем текущую базу данных
        $stmt = $pdo->prepare("SELECT DATABASE() as current_db");
        $stmt->execute();
        $currentDb = $stmt->fetch();
        error_log("Создание администратора в базе данных: " . $currentDb['current_db']);

        // Проверяем, существует ли уже администратор
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->fetch();

        if (!$adminExists) {
            // Пароль: admin123 (захеширован с помощью password_hash)
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute(['admin', $hashedPassword, 'admin@lactofilitrum.com', 'admin']);

            if ($result) {
                error_log("Администратор по умолчанию создан успешно (логин: admin, пароль: admin123)");
            } else {
                error_log("Ошибка при создании администратора: execute вернул false");
            }
        } else {
            error_log("Администратор уже существует");
        }
    } catch (PDOException $e) {
        error_log("Ошибка при создании администратора: " . $e->getMessage());
    }
}

// Функция для проверки существования таблиц
function ensureTablesExist($pdo) {
    $requiredTables = ['users', 'login_logs', 'podcasts', 'remission_library', 'clients'];
    $missingTables = [];

    // Проверяем какие таблицы отсутствуют
    foreach ($requiredTables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if (!$result->fetch()) {
            $missingTables[] = $table;
        }
    }

    // Создаем недостающие таблицы по отдельности
    if (!empty($missingTables)) {
        error_log("Отсутствуют таблицы: " . implode(', ', $missingTables) . " - создаем...");

        if (in_array('users', $missingTables)) {
            try {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        email VARCHAR(100),
                        role ENUM('admin', 'user') DEFAULT 'user',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        last_login TIMESTAMP NULL,
                        is_active BOOLEAN DEFAULT TRUE
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_username ON users(username)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_email ON users(email)");
                error_log("Таблица users создана");
            } catch (PDOException $e) {
                error_log("Ошибка создания таблицы users: " . $e->getMessage());
            }
        }

        if (in_array('login_logs', $missingTables)) {
            try {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS login_logs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT,
                        username VARCHAR(50),
                        ip_address VARCHAR(45),
                        user_agent TEXT,
                        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        success BOOLEAN DEFAULT FALSE,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_login_time ON login_logs(login_time)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_user_id ON login_logs(user_id)");
                error_log("Таблица login_logs создана");
            } catch (PDOException $e) {
                error_log("Ошибка создания таблицы login_logs: " . $e->getMessage());
            }
        }

        if (in_array('podcasts', $missingTables)) {
            try {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS podcasts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        slug VARCHAR(255) DEFAULT NULL,
                        description TEXT,
                        podcasts_text TEXT,
                        image VARCHAR(500),
                        author VARCHAR(255),
                        author_photo VARCHAR(500),
                        button_link VARCHAR(500),
                        additional_link VARCHAR(500),
                        extra_link VARCHAR(500),
                        video_path VARCHAR(500),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_podcasts_title ON podcasts(title)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_podcasts_author ON podcasts(author)");
                error_log("Таблица podcasts создана");
            } catch (PDOException $e) {
                error_log("Ошибка создания таблицы podcasts: " . $e->getMessage());
            }
        }

        if (in_array('remission_library', $missingTables)) {
            try {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS remission_library (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        image VARCHAR(500),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_remission_title ON remission_library(title)");
                error_log("Таблица remission_library создана");
            } catch (PDOException $e) {
                error_log("Ошибка создания таблицы remission_library: " . $e->getMessage());
            }
        }

        if (in_array('clients', $missingTables)) {
            try {
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS clients (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        surname VARCHAR(100) NOT NULL,
                        name VARCHAR(100) NOT NULL,
                        patronymic VARCHAR(100) DEFAULT NULL,
                        specialty VARCHAR(255) DEFAULT NULL,
                        phone VARCHAR(50) DEFAULT NULL,
                        email VARCHAR(255) NOT NULL,
                        city VARCHAR(255) DEFAULT NULL,
                        consent_personal TINYINT(1) DEFAULT 0,
                        consent_ads TINYINT(1) DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_clients_email ON clients(email)");
                $pdo->exec("CREATE INDEX IF NOT EXISTS idx_clients_created ON clients(created_at)");
                error_log("Таблица clients создана");
            } catch (PDOException $e) {
                error_log("Ошибка создания таблицы clients: " . $e->getMessage());
            }
        }
    }

    // Миграции для таблицы podcasts: выполняется всегда (таблица уже есть или только создана)
    // Проверяем существование таблицы podcasts перед выполнением миграций
    try {
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'podcasts'");
        if ($tableCheck && $tableCheck->rowCount() > 0) {
            // Миграция video_path
            $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'video_path'");
            if ($stmt && $stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE podcasts ADD COLUMN video_path VARCHAR(500) DEFAULT NULL AFTER additional_link");
                error_log("Колонка video_path добавлена в podcasts (миграция ensureTablesExist)");
            }
            
            // Миграция extra_link
            $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'extra_link'");
            if ($stmt && $stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE podcasts ADD COLUMN extra_link VARCHAR(500) DEFAULT NULL AFTER additional_link");
                error_log("Колонка extra_link добавлена в podcasts (миграция ensureTablesExist)");
            }
            
            // Миграция slug
            $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'slug'");
            if ($stmt && $stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE podcasts ADD COLUMN slug VARCHAR(255) DEFAULT NULL AFTER title");
                try {
                    $pdo->exec("CREATE UNIQUE INDEX idx_podcasts_slug ON podcasts(slug)");
                } catch (PDOException $e) { /* индекс уже есть */ }
                error_log("Колонка slug добавлена в podcasts (миграция ensureTablesExist)");
            }
            // Обратная заливка slug для старых записей (где slug IS NULL или пустой)
            $stmt = $pdo->query("SELECT id, title FROM podcasts WHERE slug IS NULL OR slug = ''");
            if ($stmt) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $baseSlug = slugify($row['title']);
                    $slug = $baseSlug;
                    $n = 2;
                    while (true) {
                        $chk = $pdo->prepare("SELECT id FROM podcasts WHERE slug = ? AND id != ? LIMIT 1");
                        $chk->execute([$slug, (int) $row['id']]);
                        if (!$chk->fetch()) break;
                        $slug = $baseSlug . '-' . $n;
                        $n++;
                    }
                    $up = $pdo->prepare("UPDATE podcasts SET slug = ? WHERE id = ?");
                    $up->execute([$slug, (int) $row['id']]);
                }
                if (count($rows) > 0) {
                    error_log("Обратная заливка slug: обновлено " . count($rows) . " подкастов");
                }
            }
            
            // Миграция podcasts_text - выполняется автоматически при каждом подключении
            $stmt = $pdo->query("SHOW COLUMNS FROM podcasts LIKE 'podcasts_text'");
            if ($stmt && $stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE podcasts ADD COLUMN podcasts_text TEXT DEFAULT NULL AFTER description");
                error_log("Колонка podcasts_text добавлена в podcasts (миграция ensureTablesExist)");
            }
        }
    } catch (PDOException $e) {
        error_log("Ошибка миграции для podcasts: " . $e->getMessage());
    }

    // Всегда проверяем администратора, независимо от того, были ли созданы таблицы
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->fetch();

        if (!$adminExists) {
            error_log("Администратор не найден, создаем...");
            createDefaultAdmin($pdo);
        } else {
            error_log("Администратор уже существует");
        }
    } catch (PDOException $e) {
        error_log("Ошибка проверки администратора: " . $e->getMessage());
    }
}

// Функция для подключения к базе данных
function connectDB() {
    static $conn = null;

    if ($conn === null) {
        // Инициализируем базу данных при первом подключении
        initializeDatabase();

        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    return $conn;
}

// Функция для очистки входных данных
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Транслит кириллицы → латиница для slug (SEO-friendly URL)
function slugify($str) {
    $str = trim($str);
    if ($str === '') return 'podcast';
    $map = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
        'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
        'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    ];
    $str = strtr($str, $map);
    $str = mb_strtolower($str, 'UTF-8');
    $str = preg_replace('/[^a-z0-9]+/u', '-', $str);
    $str = trim($str, '-');
    return $str === '' ? 'podcast' : $str;
}

// Функция для проверки авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Функция для проверки роли администратора
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Функция для перенаправления
function redirect($url) {
    header("Location: $url");
    exit();
}

// Функция для генерации CSRF токена
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Функция для проверки CSRF токена
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>