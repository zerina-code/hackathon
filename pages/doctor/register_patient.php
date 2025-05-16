<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register New Patient</title>
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
                    <li class="nav-item"><a class="nav-link active" href="#">Register Patient</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/patients_list.php">Patients</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/add_diagnostic_procedure.php">Add Procedure</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/create_medical_report.php">Create Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_records.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_orders.php">Medical Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/all_perscriptions.php">Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/appointment_approve.php">Appointments</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>Register New Patient</h2>

            <form method="post" action="/doctor/register_patient.php">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input name="first_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input name="last_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <button class="btn btn-success">Register</button>
            </form>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
