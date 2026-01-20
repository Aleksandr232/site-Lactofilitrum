<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тест загрузки файла</title>
</head>
<body>
    <h1>Тест загрузки файла</h1>
    <form action="php/api/podcasts.php" method="POST" enctype="multipart/form-data">
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
        <button type="submit">Загрузить</button>
    </form>
</body>
</html>