<!-- index.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/user_interfaces/assets/css/index.css">
    <title>Наш проект</title>
</head>
<body>
<div class="page-container"> <!-- добавили класс page-container -->
    <?php include('../includes/header.php'); ?>

    <main>
        <h1>Добро пожаловать на наш проект</h1>
        <p>Здесь вы можете ознакомиться с описанием, войти в систему или зарегистрироваться.</p>

        <nav>
            <ul>
                <li><a href="login.php">Войти</a></li>
                <li><a href="register.php">Зарегистрироваться</a></li>
                <li><a href="main.php">Перейти к основному контенту</a></li>
                <li><a href="description.php">Описание проекта</a></li>
                <li><a href="statistics.php">Статистика</a></li>
            </ul>
        </nav>
    </main>
    
</div> <!-- закрыли page-container -->
<?php include('../includes/footer.php'); ?>
</body>
</html>
