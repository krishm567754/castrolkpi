<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$query = strtolower(trim($_GET['q'] ?? ''));
$rows = read_json('stock.json');
$filtered = array_filter($rows, function ($row) use ($query) {
    if ($query === '') return true;
    $haystack = strtolower(json_encode($row));
    return strpos($haystack, $query) !== false;
});

function pack_size($row) {
    $pack = $row['pack'] ?? $row['size'] ?? $row['pack_size'] ?? '';
    if (preg_match('/([0-9]+\.?[0-9]*)/i', (string) $pack, $matches)) {
        return (float) $matches[1];
    }
    return 0;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="assets/style.css">
  <title>Stock</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Stock</h2>
    <form method="GET" style="max-width:420px; margin-top:8px;">
      <div class="form-group">
        <label>Item search</label>
        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Name or code">
      </div>
      <button type="submit">Search</button>
    </form>
    <div class="table-scroll" style="margin-top:16px;">
      <table class="table">
        <thead>
          <tr>
            <?php if (!empty($filtered)) foreach (array_keys(reset($filtered)) as $key): ?>
              <th><?php echo htmlspecialchars($key); ?></th>
            <?php endforeach; ?>
            <th>Liters (Calc)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($filtered)): ?>
            <tr><td colspan="99" class="placeholder">No stock rows found.</td></tr>
          <?php else: ?>
            <?php foreach ($filtered as $row): ?>
              <tr>
                <?php foreach ($row as $value): ?>
                  <td><?php echo htmlspecialchars($value); ?></td>
                <?php endforeach; ?>
                <?php
                  $qty = (float) ($row['qty'] ?? $row['quantity'] ?? 0);
                  $pack = pack_size($row);
                  $liters = $row['liters'] ?? ($qty * $pack);
                ?>
                <td><?php echo number_format((float)$liters, 2); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
