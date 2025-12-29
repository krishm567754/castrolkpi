<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$totalVolume = total_volume_current_year();
$openOrders = open_orders_last_three_days();
$stockCount = stock_item_count();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Dashboard</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="card-grid">
      <div class="card">
        <h2>Total Volume (Current Year)</h2>
        <div class="value"><?php echo number_format($totalVolume, 2); ?> L</div>
        <p class="small">Filtered by your allowed sales executives.</p>
      </div>
      <div class="card">
        <h2>Open Orders (Last 3 Days)</h2>
        <div class="value"><?php echo $openOrders; ?></div>
        <p class="small">Based on <code>open_orders.json</code>.</p>
      </div>
      <div class="card">
        <h2>Stock Items</h2>
        <div class="value"><?php echo $stockCount; ?></div>
        <p class="small">Unique rows in <code>stock.json</code>.</p>
      </div>
    </div>
    <div class="card" style="margin-top:16px;">
      <div class="card-header">
        <h2>Reports</h2>
        <span class="badge">Coming soon</span>
      </div>
      <p class="placeholder">Volume by Sales Executive, Weekly Sales, Brand Counts, and Top 10 Customers will appear here.</p>
    </div>
  </div>
</body>
</html>
