<?php
// File: pages/doctor/edit_patient.php

session_start();

// Compute project root
$projectRoot = dirname(__DIR__, 2);

// 1) Skip including db.php (database connection removed)

// 2) Skip verifying $conn

// 3) Include header for role guard
$headerFile = $projectRoot . '/includes/header.php';
if (! file_exists($headerFile)) {
    die('Header include not found: ' . htmlspecialchars($headerFile));
}
require_once $headerFile;

// 4) Ensure doctor session
if (! isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: /login.php');
    exit;
}

// 5) Get patient ID from query string
if (isset($_GET['patient']) && trim($_GET['patient']) !== '') {
    $patientId = (int) $_GET['patient'];
} else {
    die('Patient not specified.');
}

// 6) Handle form submission (DB interaction removed)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight       = $_POST['weight'];
    $bp_systolic  = $_POST['bp_systolic'];
    $bp_diastolic = $_POST['bp_diastolic'];

    // Normally here we would update the database

    header('Location: /pages/doctor/patient_history.php?patient=' . urlencode($patientId));
    exit;
}

// 7) Fetch vitals — now simulated as empty (DB logic removed)
$vitals = []; // Empty array, mock or test values can be added if needed
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/doctor/doctor.css">
    <title>Edit Patient Parameters</title>
</head>
<body>
<div class="container my-4">
    <h1>Edit Parameters for Patient #<?= htmlspecialchars($patientId) ?></h1>
    <form method="POST">

