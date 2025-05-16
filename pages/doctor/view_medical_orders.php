<?php
session_start();
$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: /login.php');
    exit;
}

// TODO: fetch medical orders related to this doctor
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Medical Orders</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Medical Orders</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Patient</th>
            <th>Order</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <!-- loop orders -->
        </tbody>
    </table>
</div>
</body>
</html>
