<?php
// Принудительно создаем папки с проверками
$baseDir = __DIR__;
$uploadBase = $baseDir . '/uploads';
$uploadPodcasts = $baseDir . '/uploads/podcasts';

echo "Base directory: $baseDir<br>";
echo "Upload base: $uploadBase<br>";
echo "Upload podcasts: $uploadPodcasts<br><br>";

// Создаем папку uploads
if (!file_exists($uploadBase)) {
    echo "Creating uploads folder...<br>";
    $result = mkdir($uploadBase, 0777, true);
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "<br>";
    if (!$result) {
        echo "Error: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "Uploads folder already exists<br>";
}

// Создаем папку podcasts
if (!file_exists($uploadPodcasts)) {
    echo "Creating podcasts folder...<br>";
    $result = mkdir($uploadPodcasts, 0777, true);
    echo "Result: " . ($result ? "SUCCESS" : "FAILED") . "<br>";
    if (!$result) {
        echo "Error: " . error_get_last()['message'] . "<br>";
    }
} else {
    echo "Podcasts folder already exists<br>";
}

echo "<br>Permissions:<br>";
echo "Base folder writable: " . (is_writable($uploadBase) ? "YES" : "NO") . "<br>";
echo "Podcasts folder writable: " . (is_writable($uploadPodcasts) ? "YES" : "NO") . "<br>";

echo "<br>Contents of uploads folder:<br>";
$files = scandir($uploadBase);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "- $file<br>";
    }
}
?>