<?php
if (session_status() == PHP_SESSION_NONE) {
    // Если сессия не активна, начинаем её
    session_start();
}

// Проверяем, был ли нажат выход
if (isset($_POST['logout'])) {
    // Уничтожаем сессию
    session_destroy();
    // Перенаправляем пользователя на главную страницу
    header("Location: ../pages/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/header.css">
    <title>My Project</title>
</head>
<body>
<header>
    <div class="logo">
        <a href="/user_interfaces/pages/index.php" class="logo-text">My Logo</a>
    </div>

    <nav>
        <ul>
            <?php
            // Проверяем, авторизован ли пользователь
            if (isset($_SESSION['user_id'])) {
                echo '<li><form method="post" action=""><button type="submit" name="logout">Выйти</button></form></li>';
            } ?>
        </ul>
    </nav>
</header>
</body>
</html>
