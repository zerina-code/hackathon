<?php
// File: pages/doctor/edit_patient.php

session_start();

// 1) Compute project root (two levels up from pages/doctor)
$projectRoot = dirname(__DIR__, 2);

// 2) Include config.php
$configFile = $projectRoot . '/config.php';
if (! file_exists($configFile)) {
    die('Config not found: ' . htmlspecialchars($configFile));
}
require_once $configFile;

// 3) Verify $pdo exists
if (! isset($pdo) || ! $pdo instanceof PDO) {
    die('Database connection ($pdo) is not set up correctly.');
}

// 4) Include header (role guard)
$headerFile = $projectRoot . '/includes/header.php';
if (! file_exists($headerFile)) {
    die('Header include not found: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 5) Get patient ID from query string (no ?? operator)
if (isset($_GET['patient']) && trim($_GET['patient']) !== '') {
    $patientId = $_GET['patient'];
} else {
    die('Patient not specified.');
}

// 6) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight      = $_POST['weight'];
    $bp_systolic = $_POST['bp_systolic'];
    $bp_diastolic= $_POST['bp_diastolic'];

    $update = $pdo->prepare(
        "UPDATE vitals
           SET weight       = :w,
               bp_systolic  = :sys,
               bp_diastolic = :dia,
               timestamp    = NOW()
         WHERE patient_id   = :id"
    );
    $update->execute([
        'w'   => $weight,
        'sys' => $bp_systolic,
        'dia' => $bp_diastolic,
        'id'  => $patientId
    ]);

    header('Location: /pages/doctor/patient_history.php?patient=' . urlencode($patientId));
    exit;
}

// 7) Fetch the most recent vitals (if any)
$stmt = $pdo->prepare(
    "SELECT weight, bp_systolic, bp_diastolic
     FROM vitals
     WHERE patient_id = :id
     ORDER BY timestamp DESC
     LIMIT 1"
);
$stmt->execute(['id' => $patientId]);
$vitals = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Patient Parameters</title>
</head>
<body>
<div class="container my-4">
    <h1>Edit Parameters for Patient #<?= htmlspecialchars($patientId) ?></h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Weight (kg)</label>
            <input
                name="weight"
                type="number"
                step="0.1"
                class="form-control"
                value="<?= isset($vitals['weight']) ? htmlspecialchars($vitals['weight']) : '' ?>"
                required>
        </div>

        <div class="mb-3">
            <label class="form-label">Blood Pressure</label>
            <div class="d-flex gap-2">
                <input
                    name="bp_systolic"
                    type="number"
                    class="form-control"
                    placeholder="Systolic"
                    value="<?= isset($vitals['bp_systolic']) ? htmlspecialchars($vitals['bp_systolic']) : '' ?>"
                    required>
                <input
                    name="bp_diastolic"
                    type="number"
                    class="form-control"
                    placeholder="Diastolic"
                    value="<?= isset($vitals['bp_diastolic']) ? htmlspecialchars($vitals['bp_diastolic']) : '' ?>"
                    required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Parameters</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
