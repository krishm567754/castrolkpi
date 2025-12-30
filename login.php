<?php
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = authenticate($username, $password);
    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['allowed_sales_execs'] = $user['allowed_sales_execs'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Login</title>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($error): ?>
      <p class="small" style="color: var(--danger);"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit">Sign In</button>
    </form>
    <p class="small" style="margin-top: 8px;">Use the credentials stored in <code>users.json</code>.</p>
  </div>
</body>
</html>
