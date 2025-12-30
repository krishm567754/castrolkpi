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
    <div class="page-header">
      <div>
        <div class="eyebrow">Admin</div>
        <h1 style="margin: 0;">User Management</h1>
        <p class="subtitle">Manage roles, permissions, and sales executive scope. Mobile-friendly and fast to fill.</p>
      </div>
      <div class="actions">
        <a class="pill-btn" href="#user-form">➕ Add User</a>
      </div>
    </div>

    <div id="user-form" class="card" style="margin-top:12px; max-width:860px;">
      <div class="card-header" style="margin-bottom: 10px;">
        <h2 style="margin:0;">Create or Update</h2>
        <span class="badge">Instant save on submit</span>
      </div>
      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="e.g. se1 or admin">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="text" name="password" required placeholder="Set or reset password">
          </div>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>Role</label>
            <select name="role">
              <option value="admin">Admin (full control)</option>
              <option value="user">User (restricted)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Sales Executive Access</label>
            <p class="small" style="margin:4px 0 8px;">Choose ALL for full access or pick individuals.</p>
            <div class="checkbox-grid">
              <label class="checkbox-pill"><input type="checkbox" name="allowed[]" value="ALL"> <span>ALL (Full Access)</span></label>
              <?php foreach ($salesExecs as $exec): ?>
                <label class="checkbox-pill"><input type="checkbox" name="allowed[]" value="<?php echo htmlspecialchars($exec); ?>"> <span><?php echo htmlspecialchars($exec); ?></span></label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;">
          <button type="submit">Create User</button>
          <button type="reset" class="ghost">Reset</button>
        </div>
      </form>
    </div>

    <div class="card" style="margin-top:16px;">
      <div class="card-header" style="margin-bottom: 6px;">
        <div>
          <h2 style="margin:0;">Users List</h2>
          <p class="small" style="margin:4px 0 0;">All registered users with their permissions.</p>
        </div>
        <span class="badge">Live</span>
      </div>
      <div class="stacked-list">
        <?php foreach ($users as $user): ?>
          <div class="list-item">
            <div style="display:flex; justify-content:space-between; gap:8px; flex-wrap:wrap; align-items:center;">
              <div>
                <div class="title"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="meta">Sales Exec Access: <?php echo htmlspecialchars(implode(', ', $user['allowed_sales_execs'])); ?></div>
              </div>
              <div class="tag-list">
                <span class="badge"><?php echo htmlspecialchars($user['role']); ?></span>
                <?php if ($user['username'] === ($_SESSION['username'] ?? '')): ?>
                  <span class="badge">Current User</span>
                <?php else: ?>
                  <a class="badge" style="color: var(--danger);" href="?delete=<?php echo urlencode($user['username']); ?>">Delete</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>
