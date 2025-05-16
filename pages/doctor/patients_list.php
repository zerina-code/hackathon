<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Patients List</title>
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
                    <li class="nav-item"><a class="nav-link active" href="#">Patients</a></li>
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
            <h2>All Patients</h2>

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>History</th>
                </tr>
                </thead>
                <tbody>
                <!-- Static example rows (replace with dynamic PHP or JS later) -->
                <tr>
                    <td>Jane Doe</td>
                    <td>jane@example.com</td>
                    <td><a href="/doctor/patient_history.php?patient=1" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <tr>
                    <td>Mark Allen</td>
                    <td>mark@example.com</td>
                    <td><a href="/doctor/patient_history.php?patient=2" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <tr>
                    <td>Sarah Black</td>
                    <td>sarah@example.com</td>
                    <td><a href="/doctor/patient_history.php?patient=3" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                </tbody>
            </table>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
