<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тест загрузки файла</title>
</head>
<body>
    <h1>Тест загрузки файла</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>Результаты загрузки:</h2>";

        echo "<h3>POST данные:</h3>";
        echo "<pre>" . print_r($_POST, true) . "</pre>";

        echo "<h3>FILES данные:</h3>";
        echo "<pre>" . print_r($_FILES, true) . "</pre>";

        echo "<h3>Проверка папок:</h3>";
        $uploadDir = __DIR__ . '/uploads';
        $podcastsDir = __DIR__ . '/uploads/podcasts';

        echo "Upload dir exists: " . (file_exists($uploadDir) ? "YES" : "NO") . "<br>";
        echo "Podcasts dir exists: " . (file_exists($podcastsDir) ? "YES" : "NO") . "<br>";
        echo "Upload dir writable: " . (is_writable($uploadDir) ? "YES" : "NO") . "<br>";
        echo "Podcasts dir writable: " . (is_writable($podcastsDir) ? "YES" : "NO") . "<br>";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            echo "Created uploads dir<br>";
        }

        if (!file_exists($podcastsDir)) {
            mkdir($podcastsDir, 0777, true);
            echo "Created podcasts dir<br>";
        }

        // Пробуем сохранить файл
        if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['test_file']['tmp_name'];
            $fileName = uniqid('test_', true) . '_' . $_FILES['test_file']['name'];
            $destination = $podcastsDir . '/' . $fileName;

            echo "<h3>Попытка сохранения файла:</h3>";
            echo "Source: $tmpName<br>";
            echo "Destination: $destination<br>";
            echo "File exists at tmp: " . (file_exists($tmpName) ? "YES" : "NO") . "<br>";

            if (move_uploaded_file($tmpName, $destination)) {
                echo "<strong style='color: green'>SUCCESS: File saved!</strong><br>";
                echo "Saved to: <a href='uploads/podcasts/$fileName' target='_blank'>$fileName</a><br>";
            } else {
                echo "<strong style='color: red'>FAILED: Could not save file</strong><br>";
                echo "Last error: " . (error_get_last()['message'] ?? 'unknown') . "<br>";
            }
        }
    }
    ?>

    <h2>Тест 1: Простая форма (submit)</h2>
    <form method="POST" enctype="multipart/form-data" action="php/api/podcasts.php">
        <div>
            <label for="title">Название:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="image">Картинка:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div>
            <label for="author">Автор:</label>
            <input type="text" id="author" name="author">
        </div>
        <button type="submit">Загрузить через форму</button>
    </form>

    <hr>

    <h2>Тест 2: JavaScript отправка</h2>
    <div id="js-form">
        <div>
            <label for="js_title">Название:</label>
            <input type="text" id="js_title">
        </div>
        <div>
            <label for="js_image">Картинка:</label>
            <input type="file" id="js_image" accept="image/*">
        </div>
        <div>
            <label for="js_author">Автор:</label>
            <input type="text" id="js_author">
        </div>
        <button onclick="testJSUpload()">Загрузить через JS</button>
    </div>

    <script>
    function testJSUpload() {
        const formData = new FormData();
        formData.append('title', document.getElementById('js_title').value);
        formData.append('author', document.getElementById('js_author').value);

        const imageInput = document.getElementById('js_image');
        if (imageInput.files[0]) {
            formData.append('image', imageInput.files[0]);
        }

        console.log('Sending via JS...');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        fetch('php/api/podcasts.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            alert('Результат: ' + JSON.stringify(data));
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка: ' + error);
        });
    }
    </script>
</body>
</html>