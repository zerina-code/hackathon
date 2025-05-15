<?php
//session_start();
//// Ensure user is logged in and role is doctor
//if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
//    header('Location: login.php');
//    exit;
//}
//
////require_once 'config.php';
//require_once __DIR__ . '/../../config.php';
//require_once __DIR__ . '/../../includes/header.php';
//
//$doctorId = $_SESSION['user_id'];
//
//// Fetch patients assigned to this doctor
//$stmt = $pdo->prepare("SELECT u.id, u.name, u.email FROM users u
//    JOIN appointments a ON a.patient_id = u.id
//    WHERE a.doctor_id = :docId");
//$stmt->execute(['docId' => $doctorId]);
//$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// File: pages/doctor/doctor_dashboard.php

// 1) Start session immediately so you can read $_SESSION
session_start();

// 2) Figure out your project root (two levels up: from pages/doctor → hackathon root)
$projectRoot = dirname(__DIR__, 2);

// 3) Load config.php (must define $pdo)
$configFile = $projectRoot . '/config.php';
if (!file_exists($configFile)) {
    die('Configuration file not found: ' . htmlspecialchars($configFile));
}
require_once $configFile;

// 4) Optionally verify $pdo is set
if (!isset($pdo) || !$pdo instanceof PDO) {
    die('Database connection ($pdo) is not configured correctly.');
}

// 5) Load your header which enforces “doctor only”
$headerFile = $projectRoot . '/includes/header.php';
if (!file_exists($headerFile)) {
    die('Header include not found: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 6) Now you can safely use $pdo and $_SESSION
$doctorId = $_SESSION['user_id'];

// 7) Fetch patients assigned to this doctor
$stmt = $pdo->prepare("
    SELECT u.id, u.name, u.email
    FROM users u
    JOIN appointments a ON a.patient_id = u.id
    WHERE a.doctor_id = :docId
");
$stmt->execute(['docId' => $doctorId]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Doctor Dashboard</title>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Welcome, Dr. <?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
    </div>
</nav>

<div class="container my-4">
    <h2>Your Patients</h2>
    <table class="table table-striped">
        <thead>
        <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#reportModal" data-patient-id="<?= $p['id'] ?>" data-patient-name="<?= htmlspecialchars($p['name']) ?>">New Report</button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#prescriptionModal" data-patient-id="<?= $p['id'] ?>" data-patient-name="<?= htmlspecialchars($p['name']) ?>">New Prescription</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="reportForm" action="api/create_report.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">New Medical Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                <input type="hidden" name="patient_id" id="reportPatientId">
                <div class="mb-3">
                    <label for="reportFindings" class="form-label">Findings</label>
                    <textarea name="findings" id="reportFindings" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="reportRecommendations" class="form-label">Recommendations</label>
                    <textarea name="recommendations" id="reportRecommendations" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Prescription Modal -->
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="prescriptionForm" action="api/create_prescription.php" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionModalLabel">New Prescription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="doctor_id" value="<?= $doctorId ?>">
                <input type="hidden" name="patient_id" id="prescPatientId">
                <div class="mb-3">
                    <label for="prescContent" class="form-label">Prescription Details</label>
                    <textarea name="content" id="prescContent" class="form-control" rows="5" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Prescription</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Populate modal fields when opened
    var reportModal = document.getElementById('reportModal');
    reportModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('reportPatientId').value = button.getAttribute('data-patient-id');
        reportModal.querySelector('.modal-title').textContent = 'New Medical Report for ' + button.getAttribute('data-patient-name');
    });

    var prescModal = document.getElementById('prescriptionModal');
    prescModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('prescPatientId').value = button.getAttribute('data-patient-id');
        prescModal.querySelector('.modal-title').textContent = 'New Prescription for ' + button.getAttribute('data-patient-name');
    });
</script>
</body>
</html>
