<?php
// main.php

// Подключение к базе данных
include '../includes/db.php';
include '../includes/header.php';

// Начало сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <title>Your Page Title</title>
    <link rel="stylesheet" type="text/css" href="/user_interfaces/assets/css/main.css">
</head>
<body>
<?php
// Обработка отправки ответов на тест
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_test'])) {
        // Получаем данные из формы
        $testId = $_POST['test_id'];
        $userId = $_SESSION['user_id'];

        // Получаем вопросы теста
        $stmt = $pdo->prepare("SELECT * FROM test_questions WHERE test_id = ?");
        $stmt->execute([$testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Подсчет баллов и времени
        $score = 0;
        $startTime = strtotime($_SESSION['start_time']);
        $endTime = time();

        foreach ($questions as $question) {
            $questionId = $question['id'];

            // Получаем правильные ответы
            $stmt = $pdo->prepare("SELECT id, is_correct FROM test_answers WHERE question_id = ?");
            $stmt->execute([$questionId]);
            $correctAnswers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Получаем выбранные пользователем ответы
            $userAnswerId = isset($_POST['question_' . $questionId]) ? (int)$_POST['question_' . $questionId] : null;

            // Проверяем, является ли выбранный ответ правильным
            foreach ($correctAnswers as $correctAnswer) {
                if ($correctAnswer['id'] === $userAnswerId && $correctAnswer['is_correct'] == 1) {
                    $score++;
                    break;
                }
            }
        }

        // Рассчитываем время в секундах
        $timeTaken = $endTime - $startTime;

        // Сохраняем результаты теста в базе данных
        $stmt = $pdo->prepare("INSERT INTO user_test_results (user_id, test_id, score, time_taken) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $testId, $score, $timeTaken]);

        // Перенаправляем пользователя на страницу статистики или другую нужную страницу
        header("Location: statistics.php");
        exit();
    }
}

// Отображение выбранного теста или списка тестов
if (isset($_GET['test_id'])) {
    $testId = $_GET['test_id'];

    // Получение информации о тесте
    $stmt = $pdo->prepare("SELECT * FROM psychological_tests WHERE id = ?");
    $stmt->execute([$testId]);
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($test) {
        // Вывод деталей теста
        echo "<h1>{$test['name']}</h1>";
        echo "<p>{$test['description']}</p>";

        // Получение вопросов теста
        $stmt = $pdo->prepare("SELECT * FROM test_questions WHERE test_id = ?");
        $stmt->execute([$testId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Вывод формы для прохождения теста
        echo "<form method='post' action='main.php'>";
        echo "<input type='hidden' name='test_id' value='{$testId}'>";

        foreach ($questions as $question) {
            echo "<p>{$question['question_text']}</p>";

            // Получение вариантов ответов
            $stmt = $pdo->prepare("SELECT * FROM test_answers WHERE question_id = ?");
            $stmt->execute([$question['id']]);
            $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($answers as $answer) {
                echo "<label><input type='radio' name='question_{$question['id']}' value='{$answer['id']}'> {$answer['answer_text']}</label><br>";
            }
        }

        echo "<input type='submit' name='submit_test' value='Пройти тест'>";
        echo "</form>";
    } else {
        echo "Тест не найден.";
    }
} else {
    // Вывод списка доступных тестов
    echo "<h1>Выберите тест</h1>";
    $stmt = $pdo->query("SELECT * FROM psychological_tests");
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tests as $test) {
        echo "<p><a href='main.php?test_id={$test['id']}'>{$test['name']}</a></p>";
    }
}

// Ссылка на страницу статистики
echo "<p><a href='statistics.php'>Посмотреть статистику</a></p>";
include '../includes/footer.php';
?>

</body>
</html>