<?php
// Registration page for Kinondoni Municipal Council HQ Field Student System
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kinondoni_pt_db";

$message = '';
$messageType = 'info';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $message = 'Database connection failed: ' . $conn->connect_error;
    $messageType = 'error';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '' || $phone === '') {
            $message = 'Username, password, and phone number are required.';
            $messageType = 'error';
        } else {
            $username = $conn->real_escape_string($username);
            $role = $conn->real_escape_string($role);
            $phone = $conn->real_escape_string($phone);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, position, phone_number, password_hash) VALUES ('$username', '$role', '$phone', '$passwordHash')";

            if ($conn->query($sql)) {
                $message = 'User registered successfully. You can now login.';
                $messageType = 'success';
            } else {
                $message = 'Registration failed: ' . $conn->error;
                $messageType = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Field Student Enrollment System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <h2>Kinondoni Municipal Council HQ</h2>
            <p>Field Student Enrollment Portal</p>
        </div>

<div id="messageBox" class="login-alert" style="display: <?php echo $message !== '' ? 'block' : 'none'; ?>;">
                    <?php echo htmlspecialchars($message); ?>
                </div>

        <form id="registerForm" method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username / Official Email</label>
                <input type="text" name="username" id="username" required placeholder="e.g. training.officer" autocomplete="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" name="role" id="role" required placeholder="e.g. Training Officer" autocomplete="role" value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" required placeholder="e.g. 0712345678" autocomplete="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="••••••••" autocomplete="new-password">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-secondary" style="width: 100%;"><a href="report.php" style="text-decoration: none; color: inherit;">Register</a></button>
            </div>
        </form>

        <div class="login-footer">
            <p>Restricted to authorized Training Officers & IT Staff.</p>
        </div>
        
    </div>

    <script src="login.js"></script>
</body>
</html>