<?php
$appointments = [
    ['id' => 1, 'patient' => 'John Doe', 'datetime' => '2025-05-17 10:30', 'reason' => 'Routine Checkup'],
    ['id' => 2, 'patient' => 'Jane Smith', 'datetime' => '2025-05-18 09:00', 'reason' => 'Follow-up Visit'],
    ['id' => 3, 'patient' => 'Mark Allen', 'datetime' => '2025-05-18 14:00', 'reason' => 'Consultation'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Appointments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            <li class="nav-item"><a class="nav-link" href="view_medical_records.php">Medical Records</a></li>
            <li class="nav-item"><a class="nav-link" href="view_medical_orders.php">Medical Orders</a></li>
            <li class="nav-item"><a class="nav-link" href="all_perscriptions.php">Prescriptions</a></li>
            <li class="nav-item"><a class="nav-link active" href="appointment_approve.php">Appointments</a></li>
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
            <h2 class="mb-4">Pending Appointments</h2>
            <div class="table-responsive">
                <table id="adminTable" class="table table-bordered table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td><?= htmlspecialchars($appt['patient']) ?></td>
                            <td><?= htmlspecialchars($appt['datetime']) ?></td>
                            <td><?= htmlspecialchars($appt['reason']) ?></td>
                            <td>
                                <form method="post" action="approve_appointment.php" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                    <button name="action" value="approve" class="btn btn-success btn-sm me-1">Approve</button>
                                    <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        $('#adminTable').DataTable({
            ordering: true,
            searching: true
        });
    });
</script>
</body>
</html>
