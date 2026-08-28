<?php
// Registration page for Kinondoni Municipal Council HQ Field Student System
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kinondoni_pt_db";

$message = '';
$messageType = 'info';

$conn = @new mysqli($host, $user, $pass);
if ($conn && !$conn->connect_error) {
    $conn->query("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($db);
    $conn->set_charset("utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        position VARCHAR(50) DEFAULT 'Training Officer',
        phone_number VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $role     = trim($_POST['role'] ?? 'Training Officer');
        $phone    = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '' || $phone === '') {
            $message = 'Username, password, and phone number are required.';
            $messageType = 'error';
        } else {
            $username = $conn->real_escape_string($username);
            $role     = $conn->real_escape_string($role);
            $phone    = $conn->real_escape_string($phone);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, position, phone_number, password_hash) VALUES ('$username', '$role', '$phone', '$passwordHash')";

            if ($conn->query($sql)) {
                $message = 'Account registered successfully! You can now sign in.';
                $messageType = 'success';
            } else {
                if ($conn->errno === 1062) {
                    $message = 'Username already exists. Please choose a different username.';
                } else {
                    $message = 'Registration failed: ' . $conn->error;
                }
                $messageType = 'error';
            }
        }
    }
    $conn->close();
} else {
    $message = 'Database server not connected. Registration will use demo mode.';
    $messageType = 'warning';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Field Student Enrollment System | Kinondoni MC HQ</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <img src="LOGO.png" alt="Kinondoni MC Logo" class="login-logo">
            <h2>Kinondoni Municipal Council HQ</h2>
            <p>Training Officer Registration Portal</p>
        </div>

        <?php if ($message !== ''): ?>
            <div class="login-alert <?php echo $messageType === 'success' ? 'login-alert-success' : ''; ?>" style="display: block;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username / Official Email <span class="req">*</span></label>
                <input type="text" name="username" id="username" required placeholder="e.g. juma.officer" autocomplete="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="role">Official Role / Position</label>
                <input type="text" name="role" id="role" required placeholder="e.g. Training Officer" autocomplete="organization-title" value="<?php echo htmlspecialchars($_POST['role'] ?? 'Training Officer'); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number <span class="req">*</span></label>
                <input type="tel" name="phone" id="phone" required placeholder="e.g. 0712345678" autocomplete="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password <span class="req">*</span></label>
                <input type="password" name="password" id="password" required placeholder="••••••••" autocomplete="new-password">
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Create Account</button>
                <a href="login.html" class="btn btn-secondary" style="text-align: center; text-decoration: none;">Back to Sign In</a>
            </div>
        </form>

        <div class="login-footer">
            <p>Restricted to authorized Training Officers & IT Staff.</p>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
