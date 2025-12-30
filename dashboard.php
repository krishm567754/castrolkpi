<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$totalVolumeYear = total_volume_current_year();
$totalVolumeMonth = total_volume_current_month();
$openOrders = open_orders_last_three_days();
$stockCount = stock_item_count();
$salesExecBreakdown = volume_by_sales_exec();
$brandBreakdown = brand_summary();
$topCustomers = top_customers_by_volume();
$powerCustomers = brand_customer_buckets('power1', 5);
$magnatecCustomers = brand_customer_buckets('magnatec', 5);
$crbCustomers = brand_customer_buckets('crb', 5);
$autocareCustomers = brand_customer_buckets('autocare', 5);
$activCustomers = brand_customer_buckets('activ', 5);
$highVolumeCore = high_volume_customers(9);
$monthLabel = date('F Y');
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
    <div class="hero">
      <div>
        <div class="eyebrow">Total Volume (Ltrs)</div>
        <div class="hero-value"><?php echo number_format($totalVolumeMonth, 2); ?></div>
        <div class="small">Selected month · <?php echo htmlspecialchars($monthLabel); ?></div>
      </div>
      <div class="hero-meta">
        <div class="meta-row">
          <span class="meta-label">YTD Volume</span>
          <span class="meta-value"><?php echo number_format($totalVolumeYear, 2); ?> L</span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Open Orders (3d)</span>
          <span class="meta-value"><?php echo $openOrders; ?></span>
        </div>
        <div class="meta-row">
          <span class="meta-label">Stock Items</span>
          <span class="meta-value"><?php echo $stockCount; ?></span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Reports</h2>
        <span class="badge">Quick filters</span>
      </div>
      <div class="pill-row">
        <a class="pill" href="#sales-exec">Volume by Sales Exec</a>
        <a class="pill" href="#brand-activ">'Activ' Customer Count</a>
        <a class="pill" href="#brand-power1">'Power1' Customer Count</a>
        <a class="pill" href="#brand-magnatec">'Magnatec' Customer Count</a>
        <a class="pill" href="#brand-crb">'CRB Turbomax' Count</a>
        <a class="pill" href="#high-volume">High-Volume Customers</a>
        <a class="pill" href="#brand-autocare">Autocare Count</a>
        <a class="pill" href="#brand-summary">Volume by Brand</a>
        <a class="pill" href="#top-customers">Top 10 Customers</a>
        <a class="pill" href="unbilled.php">Unbilled Customers</a>
      </div>
    </div>

    <div class="card" id="sales-exec">
      <div class="card-header">
        <h2>Volume by Sales Exec</h2>
        <span class="badge">Current year</span>
      </div>
      <?php if (empty($salesExecBreakdown)): ?>
        <p class="placeholder">No sales executive data available yet.</p>
      <?php else: ?>
        <div class="table-scroll">
          <table class="table">
            <thead>
              <tr>
                <th>Sales Executive Name</th>
                <th>Total Volume (Ltr)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($salesExecBreakdown as $exec => $volume): ?>
                <tr>
                  <td><?php echo htmlspecialchars($exec); ?></td>
                  <td><?php echo number_format($volume, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" id="brand-summary">
      <div class="card-header">
        <h2>Volume by Brand</h2>
        <span class="badge">Invoice breakdown</span>
      </div>
      <?php if (empty($brandBreakdown)): ?>
        <p class="placeholder">No brand data available yet.</p>
      <?php else: ?>
        <div class="table-scroll">
          <table class="table">
            <thead>
              <tr>
                <th>Brand</th>
                <th>Invoices</th>
                <th>Unique Customers</th>
                <th>Total Volume (Ltr)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($brandBreakdown as $brand => $data): ?>
                <tr id="<?php echo 'brand-' . strtolower(preg_replace('/\s+/', '-', $brand)); ?>">
                  <td><?php echo htmlspecialchars($brand); ?></td>
                  <td><?php echo (int) $data['invoices']; ?></td>
                  <td><?php echo (int) $data['customer_count']; ?></td>
                  <td><?php echo number_format($data['volume'], 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" id="top-customers">
      <div class="card-header">
        <h2>Top 10 Customers by Volume</h2>
        <span class="badge">Current year</span>
      </div>
      <?php if (empty($topCustomers)): ?>
        <p class="placeholder">No customer volume data available yet.</p>
      <?php else: ?>
        <div class="table-scroll">
          <table class="table">
            <thead>
              <tr>
                <th>Customer Name</th>
                <th>Total Volume (Ltr)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($topCustomers as $customer => $volume): ?>
                <tr>
                  <td><?php echo htmlspecialchars($customer); ?></td>
                  <td><?php echo number_format($volume, 2); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" id="brand-power1">
      <div class="card-header">
        <h2>Power1 Customers ≥ 5L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php include __DIR__ . '/partials/brand_bucket.php'; render_brand_bucket('Power1', $powerCustomers); ?>
    </div>

    <div class="card" id="brand-magnatec">
      <div class="card-header">
        <h2>Magnatec Customers ≥ 5L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php render_brand_bucket('Magnatec', $magnatecCustomers); ?>
    </div>

    <div class="card" id="brand-crb">
      <div class="card-header">
        <h2>CRB Turbomax Customers ≥ 5L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php render_brand_bucket('CRB Turbomax', $crbCustomers); ?>
    </div>

    <div class="card" id="brand-activ">
      <div class="card-header">
        <h2>Activ Customers ≥ 5L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php render_brand_bucket('Activ', $activCustomers); ?>
    </div>

    <div class="card" id="brand-autocare">
      <div class="card-header">
        <h2>Autocare Customers ≥ 5L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php render_brand_bucket('Autocare', $autocareCustomers); ?>
    </div>

    <div class="card" id="high-volume">
      <div class="card-header">
        <h2>High-Volume Core Customers ≥ 9L</h2>
        <span class="badge">Exec drill-down</span>
      </div>
      <?php render_brand_bucket('High-Volume', $highVolumeCore, 'Customers ≥ 9L'); ?>
    </div>
  </div>

  <div id="modal" class="modal hidden">
    <div class="modal-backdrop"></div>
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <div class="modal-title" id="modal-title">Details</div>
          <div class="modal-subtitle" id="modal-subtitle"></div>
        </div>
        <button class="icon-btn" id="modal-close" aria-label="Close">×</button>
      </div>
      <div class="table-scroll" id="modal-body"></div>
    </div>
  </div>

  <script>
    document.querySelectorAll('[data-bucket]').forEach(function(row) {
      row.addEventListener('click', function() {
        const details = JSON.parse(this.dataset.bucket);
        const exec = this.dataset.exec || 'Sales Executive';
        const label = this.dataset.label || 'Customers';
        const body = document.getElementById('modal-body');
        const title = document.getElementById('modal-title');
        const subtitle = document.getElementById('modal-subtitle');
        title.textContent = label + ' – ' + exec;
        subtitle.textContent = 'Volume per customer (Ltr)';
        let html = '<table class="table">' +
          '<thead><tr><th>Customer</th><th>Volume (Ltr)</th></tr></thead><tbody>';
        if (details.length === 0) {
          html += '<tr><td colspan="2" class="placeholder">No customers meeting this threshold yet.</td></tr>';
        } else {
          details.forEach(function(item) {
            html += '<tr><td>' + item.name + '</td><td>' + item.volume + '</td></tr>';
          });
        }
        html += '</tbody></table>';
        body.innerHTML = html;
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
