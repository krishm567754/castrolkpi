<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$query = trim($_GET['q'] ?? '');
$invoices = filter_invoices_by_scope(read_json('invoices_current.json'));
$grouped = [];
if ($query !== '') {
    foreach ($invoices as $row) {
        $invoiceNumber = strtolower((string) ($row['invoice_no'] ?? $row['invoice'] ?? ''));
        if (strpos($invoiceNumber, strtolower($query)) === false) {
            continue;
        }
        $key = $row['invoice_no'] ?? $row['invoice'] ?? 'Unknown';
        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'date' => $row['date'] ?? '',
                'invoice_no' => $key,
                'customer' => $row['customer'] ?? '',
                'sales_exec' => $row['sales_exec'] ?? ($row['sales_executive'] ?? ''),
                'lines' => [],
            ];
        }
        $grouped[$key]['lines'][] = [
            'product' => $row['product'] ?? ($row['brand'] ?? ''),
            'brand' => $row['brand'] ?? ($row['product'] ?? ''),
            'volume' => (float) ($row['volume_l'] ?? $row['volume'] ?? 0),
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
            <th>Date</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Sales Exec</th>
            <th>Total Volume (Ltr)</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($query === ''): ?>
            <tr><td colspan="5" class="placeholder">Enter an invoice number to search.</td></tr>
          <?php elseif (empty($grouped)): ?>
            <tr><td colspan="5" class="placeholder">No matches found.</td></tr>
          <?php else: ?>
            <?php foreach ($grouped as $invoice): ?>
              <?php
                $totalVolume = array_sum(array_column($invoice['lines'], 'volume'));
                $payload = htmlspecialchars(json_encode($invoice['lines']), ENT_QUOTES);
              ?>
              <tr class="clickable" data-details="<?php echo $payload; ?>" data-title="Invoice <?php echo htmlspecialchars($invoice['invoice_no'], ENT_QUOTES); ?>" data-subtitle="<?php echo htmlspecialchars($invoice['customer'], ENT_QUOTES); ?>">
                <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                <td><?php echo htmlspecialchars($invoice['invoice_no']); ?></td>
                <td><?php echo htmlspecialchars($invoice['customer']); ?></td>
                <td><?php echo htmlspecialchars($invoice['sales_exec']); ?></td>
                <td><?php echo number_format($totalVolume, 2); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="modal" class="modal hidden">
    <div class="modal-backdrop"></div>
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <div class="modal-title" id="modal-title">Invoice Details</div>
          <div class="modal-subtitle" id="modal-subtitle"></div>
        </div>
        <button class="icon-btn" id="modal-close" aria-label="Close">×</button>
      </div>
      <div class="table-scroll" id="modal-body"></div>
    </div>
  </div>

  <script>
    document.querySelectorAll('[data-details]').forEach(function(row) {
      row.addEventListener('click', function() {
        const lines = JSON.parse(this.dataset.details);
        const title = this.dataset.title || 'Invoice Details';
        const subtitle = this.dataset.subtitle || '';
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-subtitle').textContent = subtitle;
        let html = '<table class="table"><thead><tr><th>Product</th><th>Brand</th><th>Volume (Ltr)</th></tr></thead><tbody>';
        if (lines.length === 0) {
          html += '<tr><td colspan="3" class="placeholder">No line items found.</td></tr>';
        } else {
          lines.forEach(function(line) {
            html += '<tr><td>' + (line.product || '') + '</td><td>' + (line.brand || '') + '</td><td>' + Number(line.volume).toFixed(2) + '</td></tr>';
          });
        }
        html += '</tbody></table>';
        document.getElementById('modal-body').innerHTML = html;
        document.getElementById('modal').classList.remove('hidden');
      });
    });

    document.getElementById('modal-close').addEventListener('click', function() {
      document.getElementById('modal').classList.add('hidden');
    });

    document.querySelector('#modal .modal-backdrop').addEventListener('click', function() {
      document.getElementById('modal').classList.add('hidden');
    });
  </script>
</body>
</html>
