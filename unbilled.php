<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$invoices = filter_invoices_by_scope(read_json('invoices_current.json'));
$totals = [];
foreach ($invoices as $row) {
    $customer = $row['customer'] ?? 'Unknown';
    $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);
    if (!isset($totals[$customer])) {
        $totals[$customer] = 0;
    }
    $totals[$customer] += $volume;
}

$unbilled = array_filter($totals, function ($vol) {
    return $vol < 9;
});
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Unbilled</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-shell">
      <div class="page-title-row">
        <div>
          <p class="eyebrow">Gap Watch</p>
          <h1 class="page-title">Customers &lt; 9L</h1>
          <p class="page-subtitle">Spot low-volume customers quickly and jump straight into Dealer 360 to view contact, city, and GST details.</p>
        </div>
        <div class="data-badges">
          <span class="chip-soft">Source: invoices_current.json</span>
          <span class="chip-soft">Filter: &lt; 9 liters</span>
        </div>
      </div>
    </div>
    <div class="card-grid" style="margin-top:12px;">
      <?php if (empty($unbilled)): ?>
        <p class="placeholder">No customers under 9L in the current data.</p>
      <?php else: ?>
        <?php foreach ($unbilled as $customer => $vol): ?>
          <div class="card">
            <div class="card-header">
              <h2><?php echo htmlspecialchars($customer); ?></h2>
              <span class="badge">Total: <?php echo number_format($vol, 2); ?> L</span>
            </div>
            <p class="small">Dealer 360 pulls from <code>customers.json</code>.</p>
            <a class="btn" href="customer_master.php?search=<?php echo urlencode($customer); ?>">Dealer 360</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
