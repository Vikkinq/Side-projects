<?php
session_start();
// Reset any existing quiz state
$_SESSION['current_question'] = 0;
$_SESSION['score'] = 0;
$_SESSION['total_questions'] = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="quiz-container">
            <h1>Welcome to the Quiz App</h1>
            <div class="instructions">
                <h2>Instructions:</h2>
                <ul>
                    <li>This quiz contains multiple-choice questions</li>
                    <li>Select the option you think is correct</li>
                    <li>You'll see your score at the end of the quiz</li>
                    <li>Good luck!</li>
                </ul>
            </div>
            
            <form action="quiz.php" method="POST">
                <div class="form-group">
                    <label for="username">Enter your name:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <button type="submit" class="btn start-btn">Start Quiz</button>
            </form>
            
            <div class="additional-links">
                <a href="leaderboard.php" class="btn secondary-btn">View Leaderboard</a>
                <a href="admin.php" class="btn secondary-btn admin-btn">Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>