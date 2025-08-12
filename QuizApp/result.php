<?php
session_start();
include 'db_connection.php';

// Redirect if accessed directly without completing quiz
if (!isset($_SESSION['score']) || !isset($_SESSION['total_questions']) || !isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$score = $_SESSION['score'];
$total = $_SESSION['total_questions'];
$percentage = ($score / $total) * 100;

// Save result to database
$query = "INSERT INTO results (username, score, total_questions) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $username, $score, $total);
$stmt->execute();

// Determine result message based on score
if ($percentage >= 80) {
    $message = "Excellent! You're a quiz master!";
    $class = "excellent";
} elseif ($percentage >= 60) {
    $message = "Good job! You did well!";
    $class = "good";
} elseif ($percentage >= 40) {
    $message = "Not bad, but you can do better!";
    $class = "average";
} else {
    $message = "You need more practice!";
    $class = "poor";
}

// Get top 10 scores for leaderboard
$query = "SELECT username, score, total_questions, date_taken FROM results ORDER BY score DESC, date_taken DESC LIMIT 10";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="quiz-container result-container">
            <h1>Quiz Results</h1>
            
            <div class="user-result">
                <p class="username">Player: <strong><?php echo htmlspecialchars($username); ?></strong></p>
                
                <div class="score-circle <?php echo $class; ?>">
                    <span class="score-text"><?php echo $score; ?>/<?php echo $total; ?></span>
                    <span class="percentage"><?php echo round($percentage); ?>%</span>
                </div>
                
                <p class="result-message <?php echo $class; ?>"><?php echo $message; ?></p>
                
                <div class="result-details">
                    <p>You answered <strong><?php echo $score; ?></strong> out of <strong><?php echo $total; ?></strong> questions correctly.</p>
                </div>
            </div>
            
            <div class="leaderboard">
                <h2>Leaderboard</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        while ($row = $result->fetch_assoc()): 
                            $rowClass = ($row['username'] === $username) ? 'current-user' : '';
                        ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo $row['score']; ?>/<?php echo $row['total_questions']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['date_taken'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="result-actions">
                <a href="index.php" class="btn restart-btn">Try Again</a>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>