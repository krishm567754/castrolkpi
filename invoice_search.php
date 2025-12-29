<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$query = trim($_GET['q'] ?? '');
$invoices = filter_invoices_by_scope(read_json('invoices_current.json'));
$results = [];
if ($query !== '') {
    $results = array_filter($invoices, function ($row) use ($query) {
        $invoiceNumber = strtolower((string) ($row['invoice_no'] ?? $row['invoice'] ?? ''));
        return strpos($invoiceNumber, strtolower($query)) !== false;
    });
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="assets/style.css">
  <title>Invoice Search</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Invoice Search (Current Year)</h2>
    <form method="GET" style="max-width:420px; margin-top:8px;">
      <div class="form-group">
        <label>Invoice Number</label>
        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Partial match">
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
          <?php if ($query === ''): ?>
            <tr><td colspan="99" class="placeholder">Enter an invoice number to search.</td></tr>
          <?php elseif (empty($results)): ?>
            <tr><td colspan="99" class="placeholder">No matches found.</td></tr>
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
