<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin.php');
    exit();
}

// Process form submissions
$message = '';
$messageType = '';

// Delete single entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_entry'])) {
    $entry_id = $_POST['entry_id'];
    
    $query = "DELETE FROM results WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $entry_id);
    
    if ($stmt->execute()) {
        $message = "Entry deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting entry: " . $conn->error;
        $messageType = "error";
    }
}

// Clear all leaderboard entries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_leaderboard'])) {
    $query = "TRUNCATE TABLE results";
    
    if ($conn->query($query)) {
        $message = "Leaderboard cleared successfully!";
        $messageType = "success";
    } else {
        $message = "Error clearing leaderboard: " . $conn->error;
        $messageType = "error";
    }
}

// Delete selected entries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    if (isset($_POST['selected_entries']) && is_array($_POST['selected_entries'])) {
        $selected_entries = $_POST['selected_entries'];
        $placeholders = str_repeat('?,', count($selected_entries) - 1) . '?';
        
        $query = "DELETE FROM results WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($query);
        
        $types = str_repeat('i', count($selected_entries));
        $stmt->bind_param($types, ...$selected_entries);
        
        if ($stmt->execute()) {
            $message = count($selected_entries) . " entries deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting selected entries: " . $conn->error;
            $messageType = "error";
        }
    } else {
        $message = "No entries selected!";
        $messageType = "error";
    }
}

// Get all results for the leaderboard
$query = "SELECT id, username, score, total_questions, date_taken FROM results ORDER BY score DESC, date_taken DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leaderboard - Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="quiz-container admin-container">
            <h1>Manage Leaderboard</h1>
            
            <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Leaderboard Actions -->
            <div class="admin-actions">
                <form method="post" action="admin_leaderboard.php" onsubmit="return confirm('Are you sure you want to clear the entire leaderboard? This cannot be undone.');">
                    <button type="submit" name="clear_leaderboard" class="btn danger-btn">Clear Entire Leaderboard</button>
                </form>
            </div>
            
            <!-- Leaderboard Entries -->
            <div class="admin-section">
                <h2>Leaderboard Entries</h2>
                
                <?php if ($result->num_rows > 0): ?>
                <form method="post" action="admin_leaderboard.php" id="leaderboardForm">
                    <div class="bulk-actions">
                        <button type="submit" name="delete_selected" class="btn danger-btn" onclick="return confirmDeleteSelected()">Delete Selected</button>
                        <div class="select-all-container">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                            <label for="select-all">Select All</label>
                        </div>
                    </div>
                    
                    <div class="leaderboard admin-leaderboard">
                        <table>
                            <thead>
                                <tr>
                                    <th class="select-column">Select</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="select-column">
                                        <input type="checkbox" name="selected_entries[]" value="<?php echo $row['id']; ?>" class="entry-checkbox">
                                    </td>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo $row['score']; ?>/<?php echo $row['total_questions']; ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($row['date_taken'])); ?></td>
                                    <td>
                                        <form method="post" action="admin_leaderboard.php" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                            <input type="hidden" name="entry_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete_entry" class="btn small-btn danger-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <?php else: ?>
                <p class="no-results">No leaderboard entries found.</p>
                <?php endif; ?>
            </div>
            
            <div class="navigation-links">
                <a href="admin.php" class="btn secondary-btn">Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleSelectAll(source) {
            const checkboxes = document.getElementsByClassName('entry-checkbox');
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        
        function confirmDeleteSelected() {
            const checkboxes = document.getElementsByClassName('entry-checkbox');
            let selectedCount = 0;
            
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selectedCount++;
                }
            }
            
            if (selectedCount === 0) {
                alert('Please select at least one entry to delete.');
                return false;
            }
            
            return confirm(`Are you sure you want to delete ${selectedCount} selected entries?`);
        }
    </script>
</body>
</html>