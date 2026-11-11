<?php
include 'db.php';
if (isset($_SESSION['user_id'])) {
    header("Location: overview.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account && password_verify($pass, $account['password'])) {
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['username'] = $account['username'];
        $_SESSION['full_name'] = $account['full_name'];
        $_SESSION['role'] = $account['role'];
        header("Location: overview.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SchoolLedger - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <main class="login-container">
        <header class="login-header">
            <h2>SchoolLedger</h2>
            <p>Sign in to your account</p>
        </header>
        
        <?php if ($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="styled-form login-form">
            <section class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="login-input" required>
            </section>
            
            <section class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="login-input" required>
            </section>
            
            <section class="form-group checkbox-row">
                <label class="show-password-container" for="togglePassword">
                    <input type="checkbox" id="togglePassword"> 
                    <span>Show Password</span>
                </label>
            </section>
            
            <button type="submit" class="btn-submit login-btn">Sign In</button>
        </form>
    </main>

    <script src="script.js"></script>
</body>
</html>