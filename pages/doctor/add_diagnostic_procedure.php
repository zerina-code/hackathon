<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Diagnostic Procedure</title>
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
            <li class="nav-item"><a class="nav-link active" href="add_diagnostic_procedure.php">Add Procedure</a></li>
            <li class="nav-item"><a class="nav-link" href="view_medical_records.php">Medical Records</a></li>
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
            <h2 class="mb-4">Add Diagnostic Procedure</h2>

            <form method="post" action="/doctor/add_diagnostic_procedure.php">
                <div class="mb-3">
                    <label for="patient" class="form-label">Patient</label>
                    <select name="patient_id" id="patient" class="form-select" required>
                        <option value="">– Select patient –</option>
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="procedure" class="form-label">Procedure Description</label>
                    <textarea name="procedure" id="procedure" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
