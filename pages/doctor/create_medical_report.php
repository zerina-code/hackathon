<?php
// File: pages/doctor/create_medical_report.php

session_start();

// 1) Compute project root (two levels up from pages/doctor)
$projectRoot = dirname(__DIR__, 2);

// 2) Skip including db.php (removed database connection)

// 3) Removed $conn verification

// 4) Include your header/role-guard
$hdr = $projectRoot . '/includes/header.php';
if (! file_exists($hdr)) {
    die('Cannot find header include at ' . htmlspecialchars($hdr));
}
require_once $hdr;

// 5) Ensure doctor is logged in
if (! isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: /login.php');
    exit;
}
$doctorId = (int) $_SESSION['user_id'];

// 6) Handle the POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId      = (int) $_POST['patient_id'];
    $findings       = $_POST['findings'];
    $recommendations= $_POST['recommendations'];

    // Since the DB connection is removed, we're skipping the insert logic

    // Redirect back to history without inserting
    header('Location: /pages/doctor/patient_history.php?patient=' . urlencode($patientId));
    exit;
}

// If someone visits via GET
echo "<p>No data posted. Please submit the report form.</p>";
