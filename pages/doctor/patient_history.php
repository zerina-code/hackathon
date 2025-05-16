<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Doctor CSS -->
    <link rel="stylesheet" href="/css/doctor/doctor.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Doctor Panel</span>
        <a href="/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<!-- Layout with Sidebar -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar py-4">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="/doctor/doctor_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/patients_list.php">Patients</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/create_medical_report.php">Create Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/add_diagnostic_procedure.php">Add Procedure</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Patient History</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_records.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_orders.php">Medical Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/all_perscriptions.php">Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/appointment_approve.php">Appointments</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1>History for Jane Doe</h1>

            <h3 class="mt-4">Appointments</h3>
            <ul class="list-group mb-4">
                <li class="list-group-item">2025-05-10 09:30 <span class="badge bg-success">Completed</span></li>
                <li class="list-group-item">2025-05-17 10:00 <span class="badge bg-warning text-dark">Scheduled</span></li>
            </ul>

            <h3>Prescriptions</h3>
            <ul class="list-group mb-4">
                <li class="list-group-item">Paracetamol 500mg, 3x/day <small class="text-muted">(2025-05-10)</small></li>
                <li class="list-group-item">Ibuprofen 200mg, 2x/day <small class="text-muted">(2025-05-05)</small></li>
            </ul>

            <h3>Medical Reports</h3>
            <ul class="list-group mb-4">
                <li class="list-group-item">
                    <strong>Findings:</strong> Elevated temperature and fatigue<br>
                    <strong>Recommendations:</strong> Rest and hydration<br>
                    <small class="text-muted">(2025-05-10)</small>
                </li>
                <li class="list-group-item">
                    <strong>Findings:</strong> Normal vital signs<br>
                    <strong>Recommendations:</strong> Continue regular checkups<br>
                    <small class="text-muted">(2025-04-28)</small>
                </li>
            </ul>

            <a href="/pages/doctor/edit_patient.php?patient=123" class="btn btn-secondary">Edit Parameters</a>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

