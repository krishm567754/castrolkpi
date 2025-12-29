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

function scoped_invoices() {
    return filter_invoices_by_scope(read_json('invoices_current.json'));
}

function total_volume_current_month() {
    $invoices = scoped_invoices();
    $year = (int) date('Y');
    $month = (int) date('n');
    $total = 0;
    foreach ($invoices as $row) {
        $date = isset($row['date']) ? parse_date($row['date']) : 0;
        if ($date && (int) date('Y', $date) === $year && (int) date('n', $date) === $month) {
            $volume = $row['volume_l'] ?? $row['volume'] ?? 0;
            $total += (float) $volume;
        }
    }
    return $total;
}

function volume_by_sales_exec() {
    $invoices = scoped_invoices();
    $totals = [];
    foreach ($invoices as $row) {
        $exec = $row['sales_exec'] ?? ($row['sales_executive'] ?? 'Unknown');
        $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);
        $totals[$exec] = ($totals[$exec] ?? 0) + $volume;
    }
    arsort($totals);
    return $totals;
}

function brand_summary() {
    $invoices = scoped_invoices();
    $brands = [];
    foreach ($invoices as $row) {
        $brand = $row['product'] ?? ($row['brand'] ?? 'Unknown');
        $customer = $row['customer'] ?? '';
        $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);
        if (!isset($brands[$brand])) {
            $brands[$brand] = [
                'invoices' => 0,
                'customers' => [],
                'volume' => 0,
            ];
        }
        $brands[$brand]['invoices'] += 1;
        if ($customer !== '') {
            $brands[$brand]['customers'][$customer] = true;
        }
        $brands[$brand]['volume'] += $volume;
    }

    // Convert customer sets to counts and sort by volume desc.
    foreach ($brands as $key => $data) {
        $brands[$key]['customer_count'] = count($data['customers']);
        unset($brands[$key]['customers']);
    }

    uasort($brands, function ($a, $b) {
        return $b['volume'] <=> $a['volume'];
    });

    return $brands;
}

function top_customers_by_volume($limit = 10) {
    $invoices = scoped_invoices();
    $totals = [];
    foreach ($invoices as $row) {
        $customer = $row['customer'] ?? 'Unknown';
        $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);
        $totals[$customer] = ($totals[$customer] ?? 0) + $volume;
    }
    arsort($totals);
    return array_slice($totals, 0, $limit, true);
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

function brand_customer_buckets($brandKeyword, $minVolumeLiters = 5) {
    $invoices = scoped_invoices();
    $buckets = [];
    foreach ($invoices as $row) {
        $brand = strtolower((string) ($row['brand'] ?? $row['product'] ?? ''));
        if ($brandKeyword !== '' && strpos($brand, strtolower($brandKeyword)) === false) {
            continue;
        }

        $customer = $row['customer'] ?? 'Unknown';
        $exec = $row['sales_exec'] ?? ($row['sales_executive'] ?? 'Unknown');
        $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);

        if (!isset($buckets[$exec])) {
            $buckets[$exec] = [
                'customers' => [],
                'total_volume' => 0,
            ];
        }

        $buckets[$exec]['customers'][$customer] = ($buckets[$exec]['customers'][$customer] ?? 0) + $volume;
        $buckets[$exec]['total_volume'] += $volume;
    }

    foreach ($buckets as $exec => $data) {
        $filtered = [];
        foreach ($data['customers'] as $customer => $volume) {
            if ($volume >= $minVolumeLiters) {
                $filtered[$customer] = $volume;
            }
        }
        $buckets[$exec]['customers'] = $filtered;
        $buckets[$exec]['count'] = count($filtered);
        $buckets[$exec]['total_volume'] = array_sum($filtered);
    }

    uksort($buckets, function ($a, $b) use ($buckets) {
        return $buckets[$b]['count'] <=> $buckets[$a]['count'];
    });

    return $buckets;
}

function high_volume_customers($minVolumeLiters = 9) {
    $invoices = scoped_invoices();
    $execBuckets = [];
    foreach ($invoices as $row) {
        $customer = $row['customer'] ?? 'Unknown';
        $exec = $row['sales_exec'] ?? ($row['sales_executive'] ?? 'Unknown');
        $volume = (float) ($row['volume_l'] ?? $row['volume'] ?? 0);
        if (!isset($execBuckets[$exec])) {
            $execBuckets[$exec] = [
                'customers' => [],
                'total_volume' => 0,
            ];
        }
        $execBuckets[$exec]['customers'][$customer] = ($execBuckets[$exec]['customers'][$customer] ?? 0) + $volume;
        $execBuckets[$exec]['total_volume'] += $volume;
    }

    foreach ($execBuckets as $exec => $data) {
        $filtered = [];
        foreach ($data['customers'] as $customer => $volume) {
            if ($volume >= $minVolumeLiters) {
                $filtered[$customer] = $volume;
            }
        }
        $execBuckets[$exec]['customers'] = $filtered;
        $execBuckets[$exec]['count'] = count($filtered);
        $execBuckets[$exec]['total_volume'] = array_sum($filtered);
    }

    uksort($execBuckets, function ($a, $b) use ($execBuckets) {
        return $execBuckets[$b]['count'] <=> $execBuckets[$a]['count'];
    });

    return $execBuckets;
}
?>
