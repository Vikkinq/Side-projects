<?php
session_start();
include 'db_connection.php';

// Get all results for the leaderboard
$query = "SELECT id, username, score, total_questions, date_taken FROM results ORDER BY score DESC, date_taken DESC";
$result = $conn->query($query);

// Get sorting parameters if provided
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'score';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validate sort and order parameters
$validSortColumns = ['username', 'score', 'date_taken'];
$validOrderValues = ['ASC', 'DESC'];

if (!in_array($sort, $validSortColumns)) {
    $sort = 'score';
}

if (!in_array($order, $validOrderValues)) {
    $order = 'DESC';
}

// Query with sorting
$query = "SELECT id, username, score, total_questions, date_taken FROM results ORDER BY $sort $order";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Leaderboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="quiz-container leaderboard-container">
            <h1>Quiz Leaderboard</h1>
            
            <div class="sort-options">
                <p>Sort by:</p>
                <a href="?sort=score&order=DESC" class="sort-link <?php echo ($sort == 'score' && $order == 'DESC') ? 'active' : ''; ?>">Highest Score</a>
                <a href="?sort=score&order=ASC" class="sort-link <?php echo ($sort == 'score' && $order == 'ASC') ? 'active' : ''; ?>">Lowest Score</a>
                <a href="?sort=date_taken&order=DESC" class="sort-link <?php echo ($sort == 'date_taken' && $order == 'DESC') ? 'active' : ''; ?>">Most Recent</a>
                <a href="?sort=date_taken&order=ASC" class="sort-link <?php echo ($sort == 'date_taken' && $order == 'ASC') ? 'active' : ''; ?>">Oldest</a>
                <a href="?sort=username&order=ASC" class="sort-link <?php echo ($sort == 'username' && $order == 'ASC') ? 'active' : ''; ?>">Name (A-Z)</a>
            </div>
            
            <div class="leaderboard full-leaderboard">
                <?php if ($result->num_rows > 0): ?>
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
                        ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo $row['score']; ?>/<?php echo $row['total_questions']; ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($row['date_taken'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="no-results">No quiz results found. Be the first to take the quiz!</p>
                <?php endif; ?>
            </div>
            
            <div class="navigation-links">
                <a href="index.php" class="btn secondary-btn">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>