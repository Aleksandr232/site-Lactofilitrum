<?php
// API для регистрации клиентов (форма на главной)
// POST — без авторизации
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешён']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$surname = isset($input['surname']) ? trim($input['surname']) : '';
$name = isset($input['name']) ? trim($input['name']) : '';
$patronymic = isset($input['patronymic']) ? trim($input['patronymic']) : '';
$specialty = isset($input['specialty']) ? trim($input['specialty']) : '';
$phone = isset($input['phone']) ? trim($input['phone']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$city = isset($input['city']) ? trim($input['city']) : '';
$consent_personal = !empty($input['consent_personal']);
$consent_ads = !empty($input['consent_ads']);

$errors = [];
if ($surname === '') $errors[] = 'Укажите фамилию';
if ($name === '') $errors[] = 'Укажите имя';
if ($email === '') $errors[] = 'Укажите email';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
if (!$consent_personal) $errors[] = 'Необходимо согласие на обработку персональных данных';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

try {
    $pdo = connectDB();

    $stmt = $pdo->prepare("
        INSERT INTO clients (surname, name, patronymic, specialty, phone, email, city, consent_personal, consent_ads)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        sanitize($surname),
        sanitize($name),
        sanitize($patronymic),
        sanitize($specialty),
        sanitize($phone),
        sanitize($email),
        sanitize($city),
        $consent_personal ? 1 : 0,
        $consent_ads ? 1 : 0
    ]);

    echo json_encode(['success' => true, 'message' => 'Регистрация успешно завершена']);
} catch (PDOException $e) {
    error_log('Ошибка сохранения клиента: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения. Попробуйте позже.']);
}
