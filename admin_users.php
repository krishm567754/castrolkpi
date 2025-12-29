<?php
require_once __DIR__ . '/auth.php';
ensure_admin();

$users = load_users();
$salesExecs = [];
$salesFile = __DIR__ . '/sales_execs.json';
if (file_exists($salesFile)) {
    $decoded = json_decode(file_get_contents($salesFile), true);
    if (is_array($decoded)) {
        $salesExecs = $decoded;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $allowed = $_POST['allowed'] ?? [];

    if (in_array('ALL', $allowed, true)) {
        $allowed = ['ALL'];
    }

    $updated = false;
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            $user['password'] = $password;
            $user['role'] = $role;
            $user['allowed_sales_execs'] = $allowed;
            $updated = true;
            break;
        }
    }
    if (!$updated) {
        $users[] = [
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'allowed_sales_execs' => $allowed,
        ];
    }
    save_users($users);
    header('Location: admin_users.php');
    exit;
}

if (isset($_GET['delete'])) {
    $target = $_GET['delete'];
    $current = $_SESSION['username'] ?? '';
    $users = array_values(array_filter($users, function ($user) use ($target, $current) {
        if ($user['username'] === $current) {
            return true; // prevent self-delete
        }
        return $user['username'] !== $target;
    }));
    save_users($users);
    header('Location: admin_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Admin Users</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>User & Role Management</h2>
    <div class="card" style="margin-top:12px; max-width:720px;">
      <form method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="text" name="password" required>
        </div>
        <div class="form-group">
          <label>Role</label>
          <select name="role">
            <option value="admin">admin</option>
            <option value="user">user</option>
          </select>
        </div>
        <div class="form-group">
          <label>Allowed Sales Executives</label>
          <div class="tag-list">
            <label><input type="checkbox" name="allowed[]" value="ALL"> ALL</label>
            <?php foreach ($salesExecs as $exec): ?>
              <label><input type="checkbox" name="allowed[]" value="<?php echo htmlspecialchars($exec); ?>"> <?php echo htmlspecialchars($exec); ?></label>
            <?php endforeach; ?>
          </div>
        </div>
        <button type="submit">Add / Update User</button>
      </form>
    </div>

    <div class="card" style="margin-top:16px;">
      <h3>Existing Users</h3>
      <div class="table-scroll">
        <table class="table">
          <thead>
            <tr>
              <th>Username</th>
              <th>Role</th>
              <th>Sales Exec Access</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars(implode(', ', $user['allowed_sales_execs'])); ?></td>
                <td>
                  <?php if ($user['username'] !== ($_SESSION['username'] ?? '')): ?>
                    <a class="badge" style="color: var(--danger);" href="?delete=<?php echo urlencode($user['username']); ?>">Delete</a>
                  <?php else: ?>
                    <span class="badge">Current User</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
