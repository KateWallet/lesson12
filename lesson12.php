<?php
$connection = new PDO('mysql:host=localhost; dbname=academy; charset=utf8', 'root', '');

if (isset($_POST['submit'])) {
    $countFiles = count($_FILES['file']['name']);

    for ($i=0; $i<$countFiles; $i++) {
    $fileName = $_FILES['file']['name'][$i];
    $fileTmpName = $_FILES['file']['tmp_name'][$i];
    $fileType = $_FILES['file']['type'][$i];
    $fileError = $_FILES['file']['error'][$i];
    $fileSize = $_FILES['file']['size'][$i];

    $fileExtension = strtolower(end(explode('.', $fileName)));
    $fileName = explode('.', $fileName)[0];
    $fileName = preg_replace('/[0-9]/', '', $fileName);
    $allowedExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedExtensions)) {
        if ($fileSize < 6000000) {
            if ($fileError === 0) {
                $connection->query("INSERT INTO `images` (`imgname`, `extension`) VALUES ('$fileName', '$fileExtension');");
                $lastId = $connection->query("SELECT MAX(id) FROM `images`");
                $lastId = $lastId->fetchAll();
                $lastId = $lastId[0][0];
                $fileNameNew = $lastId . $fileName . '.' . $fileExtension;
                $fileDestination = 'uploads/' . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);
                echo 'Успешно';
            } else {
                echo 'Что-то пошло не так';
            }
        } else {
            echo 'Слишком большой размер файла';
        }
    } else {
        echo 'Неверный тип файла';
    }
}
}

$data = $connection->query('SELECT * FROM `images`');
echo "<div style='display: flex; align-items: flex-end; flex-wrap: wrap'>";
foreach ($data as $img) {
    $delete = "delete" . $img['id'];
    $image = "uploads/" . $img['id'] . $img['imgname'] . '.' . $img['extension'];
    if (isset($_POST[$delete])) {
        $imageId = $img['id'];
        $connection->query("DELETE * FROM `academy` . `images` WHERE id = '$imageId'");
        if (file_exists($image)) {
            unlink($image);
        }
    }
    if (file_exists($image)) {
        echo "<div>";
        echo "<img src='$image' width='160' height='160' >";
        echo "<form method='post'><button name='delete" . $img['id'] . "' style='display: block; margin: auto'>Удалить</button></form></div>";
    }
}
echo "</div>";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
</head>
<body>
<form method="post" enctype="multipart/form-data">
    Вы можете загрузить до 3 изображений одновременно: <br>
    <input type="file" name="file[]" multiple>
    <button name="submit">Загрузить</button>
</form>
</body>
</html>
