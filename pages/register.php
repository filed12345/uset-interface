<?php
session_start();

// Если пользователь уже авторизован, перенаправляем на main.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include('../includes/db.php');

$errors = [];

// Проверка, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка данных формы
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка уникальности имени пользователя
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Имя пользователя уже занято. Выберите другое.";
    }

    // Проверка длины пароля
    if (strlen($password) < 8) {
        $errors[] = "Пароль должен содержать не менее 8 символов.";
    } elseif (strlen($password) > 32) {
        $errors[] = "Пароль не должен превышать 32 символа.";
    }

    // Проверка наличия специальных символов в пароле
    if (!preg_match("/[!@#\$%\^&\*\(\)_\+\-=\[\]\{\};:'\",.<>\?~]/", $password)) {
        $errors[] = "Используйте хотя бы один специальный символ в пароле.";
    }

    // Проверка подтверждения пароля
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают.";
    }

    // Проверка наличия ошибок
    if (empty($errors)) {
        // Хеширование пароля
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Вставка данных в базу данных
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);

        // Перенаправление на страницу main.php
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/user_interfaces/assets/css/register.css">
    <title>Регистрация</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<main>
    <h1>Регистрация</h1>

    <?php
    // Вывод ошибок, если они есть
    if (!empty($errors)) {
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }
    ?>

    <form method="post" action="register.php">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Подтвердите пароль:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Зарегистрироваться</button>
    </form>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
