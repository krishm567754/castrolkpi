<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$invoices = filter_invoices_by_scope(read_json('invoices_current.json'));
$threshold = strtotime('-7 days');
$recent = array_filter($invoices, function ($row) use ($threshold) {
    $date = isset($row['date']) ? parse_date($row['date']) : 0;
    return $date >= $threshold;
});
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Recent Billing</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-shell">
      <div class="page-title-row">
        <div>
          <p class="eyebrow">Billing</p>
          <h1 class="page-title">Last 7 Days</h1>
          <p class="page-subtitle">Recent billing for your sales exec scope, in a horizontal-scroll card for smaller screens.</p>
        </div>
        <div class="data-badges">
          <span class="chip-soft">Source: invoices_current.json</span>
          <span class="chip-soft">Window: 7 days</span>
        </div>
      </div>
    </div>
    <div class="table-scroll table-card">
      <table class="table">
        <thead>
          <tr>
            <?php if (!empty($recent)) foreach (array_keys(reset($recent)) as $key): ?>
              <th><?php echo htmlspecialchars($key); ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recent)): ?>
            <tr><td colspan="99" class="placeholder">No invoices in the last 7 days.</td></tr>
          <?php else: ?>
            <?php foreach ($recent as $row): ?>
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
