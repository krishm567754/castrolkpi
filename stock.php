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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Stock</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-header">
      <div>
        <div class="eyebrow">Inventory</div>
        <h1 style="margin:0;">Current Stock</h1>
        <p class="subtitle">Search stock items by name or code. Liters auto-calculated for you.</p>
      </div>
    </div>

    <form method="GET" class="card" style="max-width:520px; margin-top:4px;">
      <div class="form-group">
        <label>Search Stock</label>
        <input type="text" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search by item name or code">
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button type="submit">Search</button>
        <a class="pill-btn" href="stock.php">Reset</a>
      </div>
    </form>

    <div class="table-scroll table-card" style="margin-top:16px;">
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
