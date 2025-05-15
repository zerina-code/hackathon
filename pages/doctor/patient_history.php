<?php
// File: pages/doctor/patient_history.php

session_start();

// 1) Compute project root (two levels up from pages/doctor)
$projectRoot = dirname(__DIR__, 2);

// 2) Include config.php
$configFile = $projectRoot . '/config.php';
if (! file_exists($configFile)) {
    die('Config file not found: ' . htmlspecialchars($configFile));
}
require_once $configFile;

// 3) Verify $pdo
if (! isset($pdo) || ! $pdo instanceof PDO) {
    die('Database connection ($pdo) not configured.');
}

// 4) Include header (checks for doctor role)
$headerFile = $projectRoot . '/includes/header.php';
if (! file_exists($headerFile)) {
    die('Header include not found: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 5) Get patient ID (no ??)
if (isset($_GET['patient']) && trim($_GET['patient']) !== '') {
    $patientId = $_GET['patient'];
} else {
    die('Patient not specified.');
}

// 6) Fetch patient info
$stmt = $pdo->prepare(
    "SELECT name, email
       FROM users
      WHERE id = :id 
        AND role = 'patient'"
);
$stmt->execute(['id' => $patientId]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

// 7) Fetch appointments, prescriptions & reports
$appointments = $pdo->prepare(
    "SELECT * FROM appointments 
     WHERE patient_id = :id 
     ORDER BY scheduled_at DESC"
);
$appointments->execute(['id' => $patientId]);

$prescriptions = $pdo->prepare(
    "SELECT * FROM prescriptions 
     WHERE patient_id = :id 
     ORDER BY created_at DESC"
);
$prescriptions->execute(['id' => $patientId]);

$reports = $pdo->prepare(
    "SELECT * FROM reports 
     WHERE patient_id = :id 
     ORDER BY created_at DESC"
);
$reports->execute(['id' => $patientId]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Patient History</title>
</head>
<body>
<div class="container my-4">
    <h1>History for <?= htmlspecialchars($patient['name']) ?></h1>

    <h3>Appointments</h3>
    <ul>
        <?php foreach ($appointments as $a): ?>
            <li>
                <?= htmlspecialchars($a['scheduled_at']) ?>
                (<?= htmlspecialchars($a['status']) ?>)
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Prescriptions</h3>
    <ul>
        <?php foreach ($prescriptions as $p): ?>
            <li>
                <?= nl2br(htmlspecialchars($p['content'])) ?>
                <small>(<?= htmlspecialchars($p['created_at']) ?>)</small>
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

    <a href="/pages/doctor/edit_patient.php?patient=<?= urlencode($patientId) ?>"
       class="btn btn-secondary">
        Edit Parameters
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
