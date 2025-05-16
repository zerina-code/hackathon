<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Appointments</title>
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
                    <li class="nav-item"><a class="nav-link" href="/doctor/add_diagnostic_procedure.php">Add Procedure</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_records.php">Medical Records</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/view_medical_orders.php">Medical Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/doctor/all_perscriptions.php">Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/doctor/appointment_approve.php">Appointments</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h2 class="mb-4">Pending Appointments</h2>

            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <!-- Example static row -->
                <tr>
                    <td>John Doe</td>
                    <td>2025-05-17 10:30</td>
                    <td>Routine Checkup</td>
                    <td>
                        <form method="post" action="approve_appointment.php">
                            <input type="hidden" name="id" value="1">
                            <button name="action" value="approve" class="btn btn-success btn-sm me-1">Approve</button>
                            <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
                <!-- You can duplicate this row or populate it with JavaScript -->
                </tbody>
            </table>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

