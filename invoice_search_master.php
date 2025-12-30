<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$customerQuery = trim($_GET['customer'] ?? '');
$productQuery = trim($_GET['product'] ?? '');

$current = filter_invoices_by_scope(read_json('invoices_current.json'));
$history = filter_invoices_by_scope(read_json('invoices_history.json'));
$all = array_merge($history, $current);

$results = array_filter($all, function ($row) use ($customerQuery, $productQuery) {
    $customer = strtolower((string) ($row['customer'] ?? ''));
    $product = strtolower((string) ($row['product'] ?? $row['brand'] ?? ''));

    if ($customerQuery !== '' && strpos($customer, strtolower($customerQuery)) === false) {
        return false;
    }
    if ($productQuery !== '' && strpos($product, strtolower($productQuery)) === false) {
        return false;
    }
    return true;
});
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="assets/style.css">
  <title>Master Invoice Search</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Invoice Search (2 Years)</h2>
    <form method="GET" class="card" style="max-width:640px;">
      <div class="form-group">
        <label>Customer Name</label>
        <input type="text" name="customer" value="<?php echo htmlspecialchars($customerQuery); ?>" placeholder="Customer (partial)">
      </div>
      <div class="form-group">
        <label>Product / Brand</label>
        <input type="text" name="product" value="<?php echo htmlspecialchars($productQuery); ?>" placeholder="Product or brand (partial)">
      </div>
      <button type="submit">Search</button>
    </form>
    <div class="table-scroll" style="margin-top:16px;">
      <table class="table">
        <thead>
          <tr>
            <?php if (!empty($results)) foreach (array_keys(reset($results)) as $key): ?>
              <th><?php echo htmlspecialchars($key); ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($customerQuery === '' && $productQuery === ''): ?>
            <tr><td colspan="99" class="placeholder">Enter a customer or product to search.</td></tr>
          <?php elseif (empty($results)): ?>
            <tr><td colspan="99" class="placeholder">No matching invoices found.</td></tr>
          <?php else: ?>
            <?php foreach ($results as $row): ?>
              <tr>
                <?php foreach ($row as $value): ?>
                  <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
