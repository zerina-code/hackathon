<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom Admin Styles -->
    <link rel="stylesheet" href="../../css/admin/header.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/layout.css" />
</head>
<body>
<div class="main-wrapper d-flex">
    <!-- Sidebar -->
    <nav class="sidebar bg-dark text-white p-3">
        <div class="sidebar-header mb-4">
            <a href="#" class="navbar-brand text-white">LOGO</a>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="doctor_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="patients_list.php">Patients</a></li>
            <li class="nav-item"><a class="nav-link" href="add_diagnostic_procedure.php">Add Procedure</a></li>
            <li class="nav-item"><a class="nav-link active" href="view_medical_records.php">Medical Records</a></li>
            <li class="nav-item"><a class="nav-link" href="view_medical_orders.php">Medical Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="all_perscriptions.php">Prescriptions</a></li>
            <li class="nav-item"><a class="nav-link" href="appointment_approve.php">Appointments</a></li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div class="content-area flex-grow-1">
        <!-- Header -->
        <header class="d-flex justify-content-end align-items-center p-3 bg-white border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    <i class="fa fa-bell fa-lg"></i>
                    <span class="badge badge-danger position-absolute" style="top: -5px; right: -10px;">3</span>
                </div>
                <a href="../../php/logout.php" class="text-dark" title="Logout">
                    <i class="fas fa-sign-out-alt fa-lg"></i>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="p-4">
            <h2 class="mb-4">Patient Medical Records</h2>

            <form method="get" class="mb-4">
                <label for="patient_id" class="form-label">Select Patient</label>
                <select name="patient_id" id="patient_id" class="form-select mb-3" required>
                    <option value="">-- Choose a patient --</option>
                    <option value="1">Jane Doe</option>
                    <option value="2">Mark Allen</option>
                    <option value="3">Sarah Black</option>
                </select>
                <button class="btn btn-primary">View Records</button>
            </form>

            <!-- Placeholder -->
            <div class="alert alert-info" role="alert">
                Select a patient to view their medical records.
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
