<?php
session_start();
include 'db_connection.php';

// Set default admin credentials if not already in database
// In a real application, you would set this up securely during installation
$checkAdminQuery = "SELECT * FROM admin LIMIT 1";
$result = $conn->query($checkAdminQuery);

if ($result->num_rows == 0) {
    // Create admin table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";
    $conn->query($createTableQuery);
    
    // Insert default admin (username: admin, password: admin123)
    // In a real application, use password_hash() for secure storage
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdminQuery = "INSERT INTO admin (username, password) VALUES ('admin', '$defaultPassword')";
    $conn->query($insertAdminQuery);
}

// Check if admin is already logged in
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Process login form
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $isLoggedIn = true;
        } else {
            $loginError = 'Invalid password';
        }
    } else {
        $loginError = 'Invalid username';
    }
}

// Process logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if (!$isLoggedIn): ?>
        <!-- Login Form -->
        <div class="quiz-container admin-container">
            <h1>Admin Login</h1>
            
            <?php if ($loginError): ?>
            <div class="error-message"><?php echo $loginError; ?></div>
            <?php endif; ?>
            
            <form method="post" action="admin.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            
            <div class="navigation-links">
                <a href="index.php" class="btn secondary-btn">Back to Home</a>
            </div>
            
            <div class="admin-info">
                <p><strong>Default Admin Credentials:</strong></p>
                <p>Username: admin</p>
                <p>Password: admin123</p>
                <p class="note">Note: In a production environment, you should change these credentials immediately.</p>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Admin Dashboard -->
        <div class="quiz-container admin-container">
            <h1>Admin Dashboard</h1>
            
            <div class="admin-menu">
                <a href="admin_questions.php" class="admin-menu-item">
                    <div class="admin-icon">📝</div>
                    <div class="admin-menu-text">
                        <h3>Manage Questions</h3>
                        <p>Add, edit, or delete quiz questions</p>
                    </div>
                </a>
                
                <a href="admin_leaderboard.php" class="admin-menu-item">
                    <div class="admin-icon">🏆</div>
                    <div class="admin-menu-text">
                        <h3>Manage Leaderboard</h3>
                        <p>View and delete leaderboard entries</p>
                    </div>
                </a>
            </div>
            
            <div class="navigation-links">
                <a href="index.php" class="btn secondary-btn">Back to Home</a>
                <a href="admin.php?logout=1" class="btn danger-btn">Logout</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>