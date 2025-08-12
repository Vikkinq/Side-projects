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

// Add new question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct_answer = $_POST['correct_answer'];
    
    // Insert question
    $query = "INSERT INTO questions (question_text) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $question_text);
    
    if ($stmt->execute()) {
        $question_id = $conn->insert_id;
        
        // Insert options
        $options = [
            1 => $option1,
            2 => $option2,
            3 => $option3,
            4 => $option4
        ];
        
        $success = true;
        foreach ($options as $option_num => $option_text) {
            $is_correct = ($option_num == $correct_answer) ? 1 : 0;
            
            $query = "INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
            
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $message = "Question added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding options: " . $conn->error;
            $messageType = "error";
        }
    } else {
        $message = "Error adding question: " . $conn->error;
        $messageType = "error";
    }
}

// Delete question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id'];
    
    // Delete options first (foreign key constraint)
    $query = "DELETE FROM options WHERE question_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    
    // Then delete the question
    $query = "DELETE FROM questions WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $question_id);
    
    if ($stmt->execute()) {
        $message = "Question deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting question: " . $conn->error;
        $messageType = "error";
    }
}

// Update question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_question'])) {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $option_ids = $_POST['option_id'];
    $option_texts = $_POST['option_text'];
    $correct_answer = $_POST['correct_answer'];
    
    // Update question
    $query = "UPDATE questions SET question_text = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $question_text, $question_id);
    $stmt->execute();
    
    // Update options
    $success = true;
    foreach ($option_ids as $index => $option_id) {
        $option_text = $option_texts[$index];
        $is_correct = ($option_id == $correct_answer) ? 1 : 0;
        
        $query = "UPDATE options SET option_text = ?, is_correct = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $option_text, $is_correct, $option_id);
        
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $message = "Question updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating options: " . $conn->error;
        $messageType = "error";
    }
}

// Get all questions for display
$query = "SELECT * FROM questions ORDER BY id DESC";
$questions_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="quiz-container admin-container">
            <h1>Manage Questions</h1>
            
            <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Add New Question Form -->
            <div class="admin-section">
                <h2>Add New Question</h2>
                <form method="post" action="admin_questions.php" class="admin-form">
                    <div class="form-group">
                        <label for="question_text">Question:</label>
                        <textarea id="question_text" name="question_text" required></textarea>
                    </div>
                    
                    <div class="options-group">
                        <div class="form-group">
                            <label for="option1">Option 1:</label>
                            <input type="text" id="option1" name="option1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option2">Option 2:</label>
                            <input type="text" id="option2" name="option2" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option3">Option 3:</label>
                            <input type="text" id="option3" name="option3" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option4">Option 4:</label>
                            <input type="text" id="option4" name="option4" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="correct_answer">Correct Answer:</label>
                        <select id="correct_answer" name="correct_answer" required>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                            <option value="4">Option 4</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="add_question" class="btn">Add Question</button>
                </form>
            </div>
            
            <!-- Existing Questions -->
            <div class="admin-section">
                <h2>Existing Questions</h2>
                
                <?php if ($questions_result->num_rows > 0): ?>
                <div class="questions-list">
                    <?php while ($question = $questions_result->fetch_assoc()): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <h3>Question #<?php echo $question['id']; ?></h3>
                            <div class="question-actions">
                                <button class="btn small-btn edit-btn" onclick="toggleEdit(<?php echo $question['id']; ?>)">Edit</button>
                                <form method="post" action="admin_questions.php" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <button type="submit" name="delete_question" class="btn small-btn danger-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="question-content">
                            <p><strong>Question:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>
                            
                            <?php
                            // Get options for this question
                            $query = "SELECT * FROM options WHERE question_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $question['id']);
                            $stmt->execute();
                            $options_result = $stmt->get_result();
                            ?>
                            
                            <div class="options-list">
                                <?php while ($option = $options_result->fetch_assoc()): ?>
                                <p class="<?php echo $option['is_correct'] ? 'correct-option' : ''; ?>">
                                    <strong>Option <?php echo $option['id']; ?>:</strong> 
                                    <?php echo htmlspecialchars($option['option_text']); ?>
                                    <?php if ($option['is_correct']): ?> <span class="correct-badge">✓ Correct</span><?php endif; ?>
                                </p>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Edit Form (Hidden by default) -->
                        <div id="edit-form-<?php echo $question['id']; ?>" class="edit-form" style="display: none;">
                            <form method="post" action="admin_questions.php" class="admin-form">
                                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                
                                <div class="form-group">
                                    <label for="edit_question_<?php echo $question['id']; ?>">Question:</label>
                                    <textarea id="edit_question_<?php echo $question['id']; ?>" name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                                </div>
                                
                                <?php
                                // Reset options result
                                $stmt->execute();
                                $options_result = $stmt->get_result();
                                ?>
                                
                                <div class="options-group">
                                    <?php while ($option = $options_result->fetch_assoc()): ?>
                                    <div class="form-group">
                                        <label for="edit_option_<?php echo $option['id']; ?>">Option:</label>
                                        <input type="hidden" name="option_id[]" value="<?php echo $option['id']; ?>">
                                        <input type="text" id="edit_option_<?php echo $option['id']; ?>" name="option_text[]" value="<?php echo htmlspecialchars($option['option_text']); ?>" required>
                                        <div class="radio-group">
                                            <input type="radio" id="correct_<?php echo $option['id']; ?>" name="correct_answer" value="<?php echo $option['id']; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?>>
                                            <label for="correct_<?php echo $option['id']; ?>">Correct Answer</label>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_question" class="btn">Update Question</button>
                                    <button type="button" class="btn secondary-btn" onclick="toggleEdit(<?php echo $question['id']; ?>)">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="no-results">No questions found. Add your first question above.</p>
                <?php endif; ?>
            </div>
            
            <div class="navigation-links">
                <a href="admin.php" class="btn secondary-btn">Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script>
        function toggleEdit(questionId) {
            const editForm = document.getElementById(`edit-form-${questionId}`);
            if (editForm.style.display === 'none') {
                editForm.style.display = 'block';
            } else {
                editForm.style.display = 'none';
            }
        }
    </script>
</body>
</html>