<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$search = strtolower(trim($_GET['search'] ?? ''));
$customers = read_json('customers.json');
$filtered = array_filter($customers, function ($row) use ($search) {
    if ($search === '') return true;
    $haystack = strtolower(json_encode($row));
    return strpos($haystack, $search) !== false;
});
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="assets/style.css">
  <title>Customers</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Customer Master</h2>
    <form method="GET" style="max-width:520px; margin-top:8px;">
      <div class="form-group">
        <label>Search by name, code, city, or phone</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
      </div>
      <button type="submit">Search</button>
    </form>
    <div class="card-grid" style="margin-top:16px;">
      <?php if (empty($filtered)): ?>
        <p class="placeholder">No customers found.</p>
      <?php else: ?>
        <?php foreach ($filtered as $customer): ?>
          <div class="card">
            <div class="card-header">
              <h2><?php echo htmlspecialchars($customer['name'] ?? 'Customer'); ?></h2>
              <span class="badge"><?php echo htmlspecialchars($customer['city'] ?? ''); ?></span>
            </div>
            <p class="small">Phone: <?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></p>
            <div class="tag-list small">
              <?php foreach ($customer as $key => $value): ?>
                <span class="badge"><?php echo htmlspecialchars($key . ': ' . $value); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
