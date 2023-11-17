<?php
session_start();

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

    // Проверка наличия пользователя с введенным именем
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Проверка пароля
        if (password_verify($password, $user['password'])) {
            // Успешная аутентификация
            $_SESSION['user_id'] = $user['id'];
            header("Location: main.php");
            exit();
        } else {
            $errors[] = "Неверный пароль. Пожалуйста, попробуйте снова.";
        }
    } else {
        $errors[] = "Пользователь с таким именем не найден.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/user_interfaces/assets/css/login.css">
    <title>Вход</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<main>
    <h1>Вход</h1>

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

    <form method="post" action="login.php">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Войти</button>
    </form>
</main>

<?php include('../includes/footer.php'); ?>
</body>
</html>
