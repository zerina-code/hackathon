<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient Parameters</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="doctor_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients_list.php">Patients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_diagnostic_procedure.php">Add Procedure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_medical_records.php">Medical Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_medical_orders.php">Medical Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_perscriptions.php">Prescriptions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointment_approve.php">Appointments</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1>Edit Parameters for Patient #123</h1>

            <form method="POST" action="/doctor/edit_patient.php">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="Jane">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="Doe">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="jane@example.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="123-456-7890">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



