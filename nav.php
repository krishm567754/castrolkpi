<?php
require_once __DIR__ . '/auth.php';
?>
<nav class="nav-bar">
  <div class="brand-row">
    <div>
      <div class="eyebrow">Laxmi Hybrid ERP</div>
      <div class="brand">KPI & Reports Hub</div>
    </div>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">☰ Menu</button>
  </div>
  <div class="nav-user">
    <span class="chip role">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?></span>
    <span class="chip scope">Scope: <?php echo htmlspecialchars(scope_label()); ?></span>
    <span class="chip user">User: <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
  </div>
  <div class="nav-links" id="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="unbilled.php">Unbilled</a>
    <a href="open_orders.php">Open Orders</a>
    <a href="billing_recent.php">Last 7 Days</a>
    <a href="invoice_search.php">Invoice Search</a>
    <a href="invoice_search_master.php">Master Search</a>
    <a href="stock.php">Stock</a>
    <a href="customer_master.php">Customers</a>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
      <a href="admin_data.php">Admin Data</a>
      <a href="admin_users.php">Admin Users</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </div>
</nav>
<script>
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.getElementById('nav-links');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
      const open = navLinks.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
</script>
