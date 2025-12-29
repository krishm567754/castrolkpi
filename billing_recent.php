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
  <link rel="stylesheet" href="assets/style.css">
  <title>Recent Billing</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Last 7 Days Billing</h2>
    <div class="table-scroll">
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
