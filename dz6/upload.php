<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $height = (int)$_POST['height']; 
    $uploadedFile = $_FILES['image']; 

    // Проверка на ошибки при загрузке файла
    if ($uploadedFile['error'] != 0) {
        echo "Ошибка при загрузке файла.";
        exit;
    }

    // Проверка формата файла
    $fileType = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, ['jpg', 'jpeg', 'JPG', 'JPEG'])) {
        echo "Можно загружать только изображения в формате JPG.";
        exit;
    }

    // Создание изображения из загруженного файла
    $imagePath = $uploadedFile['tmp_name'];
    $originalImage = imagecreatefromjpeg($imagePath);
    if (!$originalImage) {
        echo "Не удалось создать изображение из файла.";
        exit;
    }

    // Получение размеров изображения
    list($originalWidth, $originalHeight) = getimagesize($imagePath);

    // Изменение размеров изображения
    $newHeight = $height;
    $newWidth = ($newHeight / $originalHeight) * $originalWidth;

    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Создание папки uploads, если она не существует
    $uploadDir = 'uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Генерация имени файла
    $filename = uniqid('resized_', true) . '.jpg';
    $savePath = $uploadDir . '/' . $filename;

    // Сохранение изображения
    if (!imagejpeg($newImage, $savePath)) {
        echo "Не удалось сохранить изображение.";
        exit;
    }

    // Очистка памяти
    imagedestroy($originalImage);
    imagedestroy($newImage);

    // Сообщение об успешном завершении
    echo "Изображение успешно загружено и изменено! <a href='$savePath'>Скачать новое изображение</a>";
}
?>