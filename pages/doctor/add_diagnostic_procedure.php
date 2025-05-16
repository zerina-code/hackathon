<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Diagnostic Procedure</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/doctor/doctor.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Doctor Panel</a>
        <div class="d-flex">
            <a href="/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- Layout with Sidebar -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar py-4">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/doctor_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/patients_list.php">Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/doctor/add_diagnostic_procedure.php">Add Procedure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/view_medical_records.php">Medical Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/view_medical_orders.php">Medical Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/all_perscriptions.php">Prescriptions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/doctor/appointment_approve.php">Appointments</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2>Add Diagnostic Procedure</h2>

            <form method="post" action="/doctor/add_diagnostic_procedure.php">
                <div class="mb-3">
                    <label for="patient" class="form-label">Patient</label>
                    <select name="patient_id" id="patient" class="form-select">
                        <option value="">– Select patient –</option>
                        <option value="1">John Doe</option>
                        <option value="2">Jane Smith</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="procedure" class="form-label">Procedure Description</label>
                    <textarea name="procedure" id="procedure" class="form-control" rows="4"></textarea>
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

