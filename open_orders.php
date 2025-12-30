<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$rows = read_json('open_orders.json');
$threshold = strtotime('-3 days');
$filtered = array_filter($rows, function ($row) use ($threshold) {
    $date = isset($row['order_date']) ? parse_date($row['order_date']) : 0;
    return $date >= $threshold;
});
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Open Orders</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-shell">
      <div class="page-title-row">
        <div>
          <p class="eyebrow">Orders</p>
          <h1 class="page-title">Open Orders</h1>
          <p class="page-subtitle">Showing the last 3 days of activity for your allowed sales executives with a mobile-friendly, scrollable table.</p>
        </div>
        <div class="data-badges">
          <span class="chip-soft">Source: open_orders.json</span>
          <span class="chip-soft">Window: 3 days</span>
        </div>
      </div>
    </div>
    <div class="table-card table-scroll">
      <table class="table">
        <thead>
          <tr>
            <?php if (!empty($filtered)) foreach (array_keys(reset($filtered)) as $key): ?>
              <th><?php echo htmlspecialchars($key); ?></th>
            <?php endforeach; ?>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($filtered)): ?>
            <tr><td colspan="99" class="placeholder">No recent open orders.</td></tr>
          <?php else: ?>
            <?php foreach ($filtered as $row): ?>
              <tr>
                <?php foreach ($row as $value): ?>
                  <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
                <td><span class="badge">⋮</span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
