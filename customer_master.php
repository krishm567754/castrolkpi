<?php
require_once __DIR__ . '/data.php';
ensure_logged_in();

$search = strtolower(trim($_GET['search'] ?? ''));
$customers = read_json('customers.json');
$filtered = array_filter($customers, function ($row) use ($search) {
    if ($search === '') return true;
    $haystack = strtolower(json_encode($row));
    return strpos($haystack, $search) !== false;
});
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Customers</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <div class="page-shell">
      <div class="page-title-row">
        <div>
          <p class="eyebrow">Customers</p>
          <h1 class="page-title">Customer Master</h1>
          <p class="page-subtitle">Search by name, code, city, or phone. Tap a row for a full profile modal—cards and tables are tuned for single-column mobile layouts.</p>
        </div>
        <div class="data-badges">
          <span class="chip-soft">Source: customers.json</span>
          <a class="btn ghost" href="customer_master.php">Reset</a>
        </div>
      </div>
    </div>

    <form method="GET" class="card glow-card" style="max-width:560px; margin-top:4px;">
      <div class="form-group">
        <label>Customer Name / Code</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Start typing to filter instantly">
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button type="submit">Search</button>
        <a class="pill-btn" href="customer_master.php">Reset</a>
      </div>
    </form>

    <div class="table-scroll table-card" style="margin-top:16px;">
      <table class="table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>City</th>
            <th>Phone</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($filtered)): ?>
            <tr><td colspan="4" class="placeholder">No customers found.</td></tr>
          <?php else: ?>
            <?php foreach ($filtered as $customer): ?>
              <?php $details = htmlspecialchars(json_encode($customer), ENT_QUOTES, 'UTF-8'); ?>
              <tr class="clickable" data-details="<?php echo $details; ?>">
                <td><?php echo htmlspecialchars($customer['code'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($customer['name'] ?? 'Customer'); ?></td>
                <td><?php echo htmlspecialchars($customer['city'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($customer['phone'] ?? ''); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal hidden" id="customer-modal" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <div class="modal-title" id="customer-modal-title">Customer Details</div>
          <div class="modal-subtitle" id="customer-modal-subtitle"></div>
        </div>
        <button class="icon-btn" type="button" id="customer-modal-close">✕</button>
      </div>
      <div id="customer-modal-body" class="small" style="display:grid; gap:6px; max-height:60vh; overflow:auto;"></div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('customer-modal');
    const modalTitle = document.getElementById('customer-modal-title');
    const modalSubtitle = document.getElementById('customer-modal-subtitle');
    const modalBody = document.getElementById('customer-modal-body');
    const modalClose = document.getElementById('customer-modal-close');

    function closeModal() {
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('[data-details]').forEach((row) => {
      row.addEventListener('click', () => {
        const details = JSON.parse(row.dataset.details || '{}');
        modalTitle.textContent = details.name || 'Customer Details';
        modalSubtitle.textContent = details.address || details.city || '';
        modalBody.innerHTML = '';
        Object.entries(details).forEach(([key, value]) => {
          const div = document.createElement('div');
          div.textContent = `${key}: ${value}`;
          modalBody.appendChild(div);
        });
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
      });
    });

    modalClose?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
  </script>
</body>
</html>
