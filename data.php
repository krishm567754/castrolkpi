<?php
require_once __DIR__ . '/auth.php';

define('DATA_DIR', __DIR__ . '/data');

function read_json($filename) {
    $path = DATA_DIR . '/' . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function filter_invoices_by_scope(array $invoices) {
    $allowed = allowed_sales_execs();
    if (in_array('ALL', $allowed, true)) {
        return $invoices;
    }
    return array_values(array_filter($invoices, function ($row) use ($allowed) {
        $salesExec = $row['sales_exec'] ?? ($row['sales_executive'] ?? '');
        foreach ($allowed as $entry) {
            if (strcasecmp($entry, $salesExec) === 0) {
                return true;
            }
        }
        return false;
    }));
}

function parse_date($value) {
    $timestamp = strtotime($value);
    return $timestamp ? $timestamp : 0;
}

function total_volume_current_year() {
    $invoices = filter_invoices_by_scope(read_json('invoices_current.json'));
    $year = (int) date('Y');
    $total = 0;
    foreach ($invoices as $row) {
        $date = isset($row['date']) ? parse_date($row['date']) : 0;
        if ($date && (int) date('Y', $date) === $year) {
            $volume = $row['volume_l'] ?? $row['volume'] ?? 0;
            $total += (float) $volume;
        }
    }
    return $total;
}

function open_orders_last_three_days() {
    $rows = read_json('open_orders.json');
    $threshold = strtotime('-3 days');
    $recent = array_filter($rows, function ($row) use ($threshold) {
        $date = isset($row['order_date']) ? parse_date($row['order_date']) : 0;
        return $date >= $threshold;
    });
    return count($recent);
}

function stock_item_count() {
    return count(read_json('stock.json'));
}
?>
