<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<!--            <li class="nav-item"><a class="nav-link" href="view_medical_records.php">Medical Records</a></li>-->
<!--            <li class="nav-item"><a class="nav-link" href="view_medical_orders.php">Medical Orders</a></li>-->
<!--            <li class="nav-item"><a class="nav-link" href="all_perscriptions.php">Prescriptions</a></li>-->
            <li class="nav-item"><a class="nav-link" href="appointment_approve.php">Appointments</a></li>
            <li class="nav-item"><a class="nav-link active" href="patient_history.php">Patient History</a></li>
        </ul>
    </nav>

    <!-- Content Area -->
    <div class="content-area flex-grow-1">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
            <h3 class="mb-0">Patient History</h3>
            <a href="../../php/logout.php" class="btn btn-outline-dark btn-sm">Logout</a>
        </header>

        <!-- Main Content -->
        <main class="p-4">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Diagnosis</th>
                        <th>Order</th>
                        <th>Prescription</th>
                        <th>Date of Visit</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>John Doe</td>
                        <td>Hypertension</td>
                        <td>Blood test – CBC</td>
                        <td>Paracetamol 500mg</td>
                        <td>2025-05-16</td>
                    </tr>
                    <tr>
                        <td>Max William</td>
                        <td>Back Pain</td>
                        <td>X-ray – Lumbar</td>
                        <td>Ibuprofen 400mg</td>
                        <td>2025-05-15</td>
                    </tr>
                    <tr>
                        <td>Taylor Swift</td>
                        <td>Allergy</td>
                        <td>Skin Prick Test</td>
                        <td>Antihistamines</td>
                        <td>2025-05-14</td>
                    </tr>
                    <tr>
                        <td>Jane Smith</td>
                        <td>Flu</td>
                        <td>Throat Swab</td>
                        <td>Tamiflu</td>
                        <td>2025-05-13</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</body>
</html>
