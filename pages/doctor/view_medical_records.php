<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Medical Records</title>
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
                    <li class="nav-item"><a class="nav-link active" href="#">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_orders.php">Medical Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/all_perscriptions.php">Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/appointment_approve.php">Appointments</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>Patient Medical Records</h2>

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

            <!-- Placeholder for records -->
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
