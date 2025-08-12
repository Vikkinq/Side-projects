<?php
session_start();
include 'db_connection.php';

// Store username if coming from index page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['current_question'] = 0;
    $_SESSION['score'] = 0;
    unset($_SESSION['question_ids']); // Reset question order if restarting quiz
}

// Check if username is set
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Generate random questions only once
if (!isset($_SESSION['question_ids'])) {
    $query = "SELECT id FROM questions ORDER BY RAND() LIMIT 10";
    $result = $conn->query($query);
    $question_ids = [];
    while ($row = $result->fetch_assoc()) {
        $question_ids[] = $row['id'];
    }
    $_SESSION['question_ids'] = $question_ids;
    $_SESSION['total_questions'] = count($question_ids);
}

// Process answer submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['option_id'])) {
    $selected_option = $_POST['option_id'];
    $query = "SELECT is_correct FROM options WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $selected_option);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['is_correct']) {
        $_SESSION['score']++;
    }
    $_SESSION['current_question']++;
    if ($_SESSION['current_question'] >= $_SESSION['total_questions']) {
        header("Location: result.php");
        exit();
    }
}

$current_index = $_SESSION['current_question'];
$question_id = $_SESSION['question_ids'][$current_index];
$query = "SELECT id, question_text FROM questions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();

$query = "SELECT id, option_text FROM options WHERE question_id = ? ORDER BY RAND()";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question['id']);
$stmt->execute();
$options_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz App</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    let countdown = 10;
    function startTimer() {
        let display = document.getElementById("time");
        let interval = setInterval(() => {
            display.textContent = countdown;
            countdown--;
            if (countdown < 0) {
                clearInterval(interval);
                alert("⏰ Time's up! Moving to next question.");
                document.getElementById("quiz-form").submit();
            }
        }, 1000);
    }
    window.onload = startTimer;
    </script>
</head>
<body>
<div class="container">
    <div class="quiz-container">
        <div class="user-info">
            <p>Player: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        </div>
        <div class="progress-bar">
            <div class="progress" style="width: <?php echo ($_SESSION['current_question'] / $_SESSION['total_questions']) * 100; ?>%"></div>
        </div>
        <div class="question-count">
            Question <?php echo $current_index + 1; ?> of <?php echo $_SESSION['total_questions']; ?>
        </div>

        <div class="timer">
            Time Remaining: <span id="time">10</span> seconds
        </div>

        <h2 class="question"><?php echo $question['question_text']; ?></h2>

        <form method="post" action="quiz.php" id="quiz-form">
            <div class="options">
                <?php while ($option = $options_result->fetch_assoc()): ?>
                    <div class="option">
                        <input type="radio" name="option_id" id="option_<?php echo $option['id']; ?>" value="<?php echo $option['id']; ?>" required>
                        <label for="option_<?php echo $option['id']; ?>"><?php echo $option['option_text']; ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="btn next-btn">Next Question</button>
        </form>
    </div>
</div>
</body>
</html>
