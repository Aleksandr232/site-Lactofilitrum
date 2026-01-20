<?php
// Создаем необходимые папки
$uploadBase = __DIR__ . '/uploads/';
$uploadPodcasts = __DIR__ . '/uploads/podcasts/';

echo "Upload base path: $uploadBase<br>";
echo "Upload podcasts path: $uploadPodcasts<br>";

if (!file_exists($uploadBase)) {
    if (mkdir($uploadBase, 0755, true)) {
        echo "Created uploads base folder<br>";
    } else {
        echo "Failed to create uploads base folder<br>";
    }
} else {
    echo "Uploads base folder already exists<br>";
}

if (!file_exists($uploadPodcasts)) {
    if (mkdir($uploadPodcasts, 0755, true)) {
        echo "Created podcasts upload folder<br>";
    } else {
        echo "Failed to create podcasts upload folder<br>";
    }
} else {
    echo "Podcasts upload folder already exists<br>";
}

echo "Base folder writable: " . (is_writable($uploadBase) ? "YES" : "NO") . "<br>";
echo "Podcasts folder writable: " . (is_writable($uploadPodcasts) ? "YES" : "NO") . "<br>";
?>