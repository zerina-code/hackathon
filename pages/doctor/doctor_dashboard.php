<?php
// File: pages/doctor/doctor_dashboard.php

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

// 5) Fake/empty patients array since DB logic is removed
$patients = []; // You can fill this manually for testing if needed
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Doctor Dashboard</title>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Welcome, Dr. <?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="/logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container my-4">
    <h2>Your Patients</h2>
    <table class="table table-striped">
        <thead>
        <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if (empty($patients)): ?>
            <tr><td colspan="3" class="text-muted">No patients available.</td></tr>
        <?php else: ?>
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
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="reportForm" action="/pages/doctor/create_medical_report.php" method="POST" class="modal-content">
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

