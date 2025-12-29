<?php
session_start();

function load_users() {
    $path = __DIR__ . '/users.json';
    if (!file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_users(array $users) {
    $path = __DIR__ . '/users.json';
    file_put_contents($path, json_encode(array_values($users), JSON_PRETTY_PRINT));
}

function authenticate($username, $password) {
    foreach (load_users() as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return $user;
        }
    }
    return null;
}

function ensure_logged_in() {
    if (empty($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }
}

function ensure_admin() {
    ensure_logged_in();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        echo 'Admin access required';
        exit;
    }
}

function allowed_sales_execs() {
    if (!isset($_SESSION['allowed_sales_execs'])) {
        return [];
    }
    return $_SESSION['allowed_sales_execs'];
}

function sales_exec_match($salesExec) {
    $allowed = allowed_sales_execs();
    if (in_array('ALL', $allowed, true)) {
        return true;
    }
    foreach ($allowed as $entry) {
        if (strcasecmp($entry, $salesExec) === 0) {
            return true;
        }
    }
    return false;
}

function scope_label() {
    $allowed = allowed_sales_execs();
    return empty($allowed) ? 'None' : implode(', ', $allowed);
}
?>
