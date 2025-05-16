<?php
// File: pages/doctor/write_prescription.php

session_start();

// 1) Compute project root (two levels up from pages/doctor)
$projectRoot = dirname(__DIR__, 2);

// 2) Include config.php
$configFile = $projectRoot . '/config.php';
if (! file_exists($configFile)) {
    die('Config.php not found at: ' . htmlspecialchars($configFile));
}
require_once $configFile;

// 3) Verify $pdo is set
if (! isset($pdo) || ! $pdo instanceof PDO) {
    die('Database connection ($pdo) not configured correctly.');
}

// 4) Include your role‐guard header
$headerFile = $projectRoot . '/includes/header.php';
if (! file_exists($headerFile)) {
    die('Header include not found at: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 5) Grab your doctor ID from session
if (! isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: /login.php');
    exit;
}
$doctorId = $_SESSION['user_id'];

// 6) Fetch all patients for the dropdown
$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'patient'");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet">
    <title>Write Prescription</title>
</head>
<body>
<div class="container my-4">
    <h1>Write Prescription</h1>
    <form action="/api/create_prescription.php" method="POST">
        <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctorId) ?>">
        <div class="mb-3">
            <label for="patientId" class="form-label">Patient</label>
            <select
                    name="patient_id"
                    id="patientId"
                    class="form-select"
                    required>
                <option value="">Select patient…</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>">
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="prescContent" class="form-label">
                Prescription Details
            </label>
            <textarea
                    name="content"
                    id="prescContent"
                    class="form-control"
                    rows="5"
                    required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">
            Save Prescription
        </button>
    </form>
</div>

<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>
</body>
</html>

