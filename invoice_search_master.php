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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Master Invoice Search</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-header">
      <div>
        <div class="eyebrow">Invoices</div>
        <h1 style="margin:0;">Master Search</h1>
        <p class="subtitle">Search current and historical invoices by customer and brand. Results are mobile-optimized.</p>
      </div>
    </div>

    <form method="GET" class="card" style="max-width:680px;">
      <div class="form-grid">
        <div class="form-group">
          <label>Customer Name</label>
          <input type="text" name="customer" value="<?php echo htmlspecialchars($customerQuery); ?>" placeholder="Customer (partial)">
        </div>
        <div class="form-group">
          <label>Product / Brand</label>
          <input type="text" name="product" value="<?php echo htmlspecialchars($productQuery); ?>" placeholder="Product or brand (partial)">
        </div>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button type="submit">Search</button>
        <a class="pill-btn" href="invoice_search_master.php">Reset</a>
      </div>
    </form>
    <div class="table-scroll table-card" style="margin-top:16px;">
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
