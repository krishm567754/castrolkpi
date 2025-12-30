<?php
function render_brand_bucket($label, array $buckets, $subtitle = 'Customers ≥ 5L') {
    if (empty($buckets)) {
        echo '<p class="placeholder">No data found for this selection.</p>';
        return;
    }
    echo '<div class="table-scroll">';
    echo '<table class="table">';
    echo '<thead><tr><th>Sales Executive</th><th>' . htmlspecialchars($subtitle) . '</th><th>Total Volume (Ltr)</th></tr></thead><tbody>';
    foreach ($buckets as $exec => $data) {
        $customerList = [];
        foreach ($data['customers'] as $customer => $volume) {
            $customerList[] = [
                'name' => htmlspecialchars($customer, ENT_QUOTES),
                'volume' => number_format($volume, 2),
            ];
        }
        $payload = htmlspecialchars(json_encode($customerList), ENT_QUOTES);
        echo '<tr class="clickable" data-bucket="' . $payload . '" data-exec="' . htmlspecialchars($exec, ENT_QUOTES) . '" data-label="' . htmlspecialchars($label, ENT_QUOTES) . '">';
        echo '<td>' . htmlspecialchars($exec) . '</td>';
        echo '<td>' . (int) ($data['count'] ?? 0) . '</td>';
        echo '<td>' . number_format($data['total_volume'] ?? 0, 2) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
?>
