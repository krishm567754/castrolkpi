<?php
require_once __DIR__ . '/auth.php';

$current = basename($_SERVER['PHP_SELF'] ?? '');
$isActive = function (string $file) use ($current): string {
    return $current === $file ? 'active' : '';
};
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
    <a class="<?php echo $isActive('dashboard.php'); ?>" href="dashboard.php">Dashboard</a>
    <a class="<?php echo $isActive('unbilled.php'); ?>" href="unbilled.php">Unbilled</a>
    <a class="<?php echo $isActive('open_orders.php'); ?>" href="open_orders.php">Open Orders</a>
    <a class="<?php echo $isActive('billing_recent.php'); ?>" href="billing_recent.php">Last 7 Days</a>
    <a class="<?php echo $isActive('invoice_search.php'); ?>" href="invoice_search.php">Invoice Search</a>
    <a class="<?php echo $isActive('invoice_search_master.php'); ?>" href="invoice_search_master.php">Master Search</a>
    <a class="<?php echo $isActive('stock.php'); ?>" href="stock.php">Stock</a>
    <a class="<?php echo $isActive('customer_master.php'); ?>" href="customer_master.php">Customers</a>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
      <a class="<?php echo $isActive('admin_data.php'); ?>" href="admin_data.php">Admin Data</a>
      <a class="<?php echo $isActive('admin_users.php'); ?>" href="admin_users.php">Admin Users</a>
    <?php endif; ?>
    <a class="<?php echo $isActive('logout.php'); ?>" href="logout.php">Logout</a>
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
