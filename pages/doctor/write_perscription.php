<?php
// File: pages/doctor/write_prescription.php

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
$doctorId = (int)$_SESSION['user_id'];

// 5) Simulated list of patients since DB logic is removed
$patients = [
    ['id' => 1, 'name' => 'John Doe'],
    ['id' => 2, 'name' => 'Jane Smith'],
    ['id' => 3, 'name' => 'Mark Lee'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Write Prescription</title>
</head>
<body>
<div class="container my-4">
    <h1>Write Prescription</h1>
    <form action="/pages/doctor/create_prescription.php" method="POST">
        <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctorId) ?>">
        <div class="mb-3">
            <label for="patientId" class="form-label">Patient</label>
            <select name="patient_id" id="patientId" class="form-select" required>
                <option value="">Select patient…</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="prescContent" class="form-label">Prescription Details</label>
            <textarea name="content" id="prescContent" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Prescription</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



