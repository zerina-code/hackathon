<?php
// File: pages/doctor/patient_history.php

session_start();

// Compute project root
$projectRoot = dirname(__DIR__, 2);

// 1) Skip including db.php (database connection removed)

// 2) Skip verifying $conn

// 3) Include header for role guard
$headerFile = $projectRoot . '/includes/header.php';
if (!file_exists($headerFile)) {
    die('Header include not found: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 4) Ensure doctor session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: /login.php');
    exit;
}

// 5) Get patient ID from query
if (isset($_GET['patient']) && trim($_GET['patient']) !== '') {
    $patientId = (int) $_GET['patient'];
} else {
    die('Patient not specified.');
}

// 6-9) Simulated data since DB queries are removed
$patient = [
    'name' => 'Sample Patient',
    'email' => 'sample@example.com'
];

$appointments = [
    ['scheduled_at' => '2024-12-01 10:00:00', 'status' => 'Completed'],
    ['scheduled_at' => '2024-11-15 14:30:00', 'status' => 'Missed']
];

$prescriptions = [
    ['content' => 'Take 1 tablet of X twice a day.', 'created_at' => '2024-12-01'],
    ['content' => 'Apply cream Y to affected area.', 'created_at' => '2024-11-16']
];

$reports = [
    [
        'findings' => 'Patient showed improvement in mobility.',
        'recommendations' => 'Continue with physiotherapy and hydration.',
        'created_at' => '2024-12-01'
    ],
    [
        'findings' => 'High blood pressure observed.',
        'recommendations' => 'Reduce salt intake and monitor BP daily.',
        'created_at' => '2024-11-16'
    ]
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Patient History</title>
</head>
<body>
<div class="container my-4">
    <h1>History for <?= htmlspecialchars($patient['name']) ?></h1>

    <h3>Appointments</h3>
    <ul>
        <?php foreach ($appointments as $a): ?>
            <li>
                <?= htmlspecialchars($a['scheduled_at']) ?> (<?= htmlspecialchars($a['status']) ?>)
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Prescriptions</h3>
    <ul>
        <?php foreach ($prescriptions as $p): ?>
            <li>
                <?= nl2br(htmlspecialchars($p['content'])) ?> <small>(<?= htmlspecialchars($p['created_at']) ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Medical Reports</h3>
    <ul>
        <?php foreach ($reports as $r): ?>
            <li>
                <strong>Findings:</strong> <?= nl2br(htmlspecialchars($r['findings'])) ?><br>
                <strong>Recommendations:</strong> <?= nl2br(htmlspecialchars($r['recommendations'])) ?>
                <small>(<?= htmlspecialchars($r['created_at']) ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="/pages/doctor/edit_patient.php?patient=<?= urlencode($patientId) ?>" class="btn btn-secondary">Edit Parameters</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
