<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

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
<!--            <li class="nav-item"><a class="nav-link active" href="view_medical_records.php">Medical Records</a></li>-->
<!--            <li class="nav-item"><a class="nav-link" href="view_medical_orders.php">Medical Orders</a></li>-->
<!--            <li class="nav-item"><a class="nav-link" href="all_perscriptions.php">Prescriptions</a></li>-->
            <li class="nav-item"><a class="nav-link" href="appointment_approve.php">Appointments</a></li>
            <li class="nav-item"><a class="nav-link" href="patient_history.php">Patient History</a></li>

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
            <h2 class="mb-4">Medical Records</h2>

            <table id="recordsTable" class="table table-bordered table-hover">
                <thead class="table-light">
                <tr>
                    <th>Patient Name</th>
                    <th>Date of Birth</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Jane Doe</td>
                    <td>1985-04-12</td>
                    <td>Hypertension</td>
                    <td>Medication + Lifestyle changes</td>
                    <td>2025-05-10</td>
                </tr>
                <tr>
                    <td>Mark Allen</td>
                    <td>1979-11-23</td>
                    <td>Diabetes</td>
                    <td>Insulin Therapy</td>
                    <td>2025-05-12</td>
                </tr>
                <tr>
                    <td>Sarah Black</td>
                    <td>1990-06-15</td>
                    <td>Allergy</td>
                    <td>Antihistamines</td>
                    <td>2025-05-14</td>
                </tr>
                </tbody>
            </table>
        </main>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#recordsTable').DataTable({
            ordering: true,
            searching: true
        });
    });
</script>
</body>
</html>

