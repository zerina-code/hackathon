<?php
// File: pages/doctor/create_medical_report.php

session_start();

// 1) Compute project root (two levels up from pages/doctor)
$projectRoot = dirname(__DIR__, 2);

// 2) Include config.php
$config = $projectRoot . '/config.php';
if (! file_exists($config)) {
    die('Cannot find config.php at ' . htmlspecialchars($config));
}
require_once $config;

// 3) Verify $pdo
if (! isset($pdo) || ! $pdo instanceof PDO) {
    die('Database connection ($pdo) not configured.');
}

// 4) Include your header/role-guard
$hdr = $projectRoot . '/includes/header.php';
if (! file_exists($hdr)) {
    die('Cannot find header include at ' . htmlspecialchars($hdr));
}
require_once $hdr;

// 5) Grab the doctor’s ID
if (! isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    // (optional) double check
    header('Location: /login.php');
    exit;
}
$doctorId = $_SESSION['user_id'];

// 6) Handle the POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId      = $_POST['patient_id'];
    $findings       = $_POST['findings'];
    $recommendations= $_POST['recommendations'];

    $stmt = $pdo->prepare(
        "INSERT INTO reports 
        (appointment_id, doctor_id, patient_id, findings, recommendations)
       VALUES 
        (NULL, :doc, :pat, :find, :rec)"
    );
    $stmt->execute([
        'doc'  => $doctorId,
        'pat'  => $patientId,
        'find' => $findings,
        'rec'  => $recommendations
    ]);

    header('Location: /pages/doctor/patient_history.php?patient=' . urlencode($patientId));
    exit;
}

// (Optional: show a form if accessed via GET)
echo "No data posted.";
