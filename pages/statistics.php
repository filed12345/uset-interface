<?php
// statistics.php

include '../includes/db.php'; // Подключение к базе данных
include '../includes/header.php'; // Включение шапки страницы

// Получаем среднюю статистику для каждого пользователя
$stmt = $pdo->query("SELECT 
                        users.id as user_id,
                        username,
                        COUNT(user_test_results.id) as tests_passed,
                        AVG(user_test_results.score) as average_score,
                        SEC_TO_TIME(AVG(user_test_results.time_taken)) as average_time_taken
                    FROM users
                    LEFT JOIN user_test_results ON users.id = user_test_results.user_id
                    GROUP BY users.id, username
                    ORDER BY average_score DESC");

$statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <title>Статистика пользователей</title>
</head>

<body>
<div class="page-container">

    <main>
        <h1>Статистика пользователей</h1>

        <?php if ($statistics) : ?>
            <table>
                <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Пройдено тестов</th>
                    <th>Средний балл</th>
                    <th>Среднее время прохождения</th>
                    <th>Рейтинг</th>
                </tr>
                </thead>
                <tbody>
                <?php $rank = 1; ?>
                <?php foreach ($statistics as $result) : ?>
                    <tr>
                        <td><?= $result['username']; ?></td>
                        <td><?= $result['tests_passed']; ?></td>
                        <td><?= number_format($result['average_score'], 2); ?></td>
                        <td><?= $result['average_time_taken']; ?></td>
                        <td><?= $rank++; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Пока нет результатов тестов.</p>
        <?php endif; ?>
    </main>
</div>
</body>
<?php include '../includes/footer.php'; ?>
</html>
