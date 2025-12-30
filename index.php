<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Castrol KPI</title>
</head>
<body>
  <div class="login-container">
    <h2>Castrol KPI Dashboard</h2>
    <p class="small">Secure ERP/CRM dashboards for invoices, open orders, stock, and customers.</p>
    <a class="btn" href="login.php">Login</a>
  </div>
</body>
</html>
