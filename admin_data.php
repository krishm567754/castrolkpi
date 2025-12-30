<?php
require_once __DIR__ . '/auth.php';
ensure_admin();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/style.css">
  <title>Admin Data</title>
</head>
<body>
  <?php include 'nav.php'; ?>
  <div class="layout">
    <h2>Admin Data Uploads</h2>
    <div class="card" style="margin-top:12px;">
      <p>This placeholder shows where Excel uploads and conversions to JSON will live. Uploaders should target:</p>
      <ul>
        <li><code>invoice_current.xlsx</code> → <code>invoices_current.json</code></li>
        <li><code>q1.xlsx</code> … <code>q7.xlsx</code> → <code>invoices_history.json</code></li>
        <li><code>open_orders.xlsx</code> → <code>open_orders.json</code></li>
        <li><code>stock.xlsx</code> → <code>stock.json</code></li>
        <li><code>customers.xlsx</code> → <code>customers.json</code></li>
      </ul>
      <p class="small">Hook file processing here when ready to wire uploads.</p>
    </div>
  </div>
</body>
</html>
