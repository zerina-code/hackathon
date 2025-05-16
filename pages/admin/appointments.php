<?php
include_once '../../components/table-component.php';
$appointments = [
    [
        'doctorName' => 'Dr. Ruby Perrin',
        'speciality' => 'Dental',
        'patientName' => 'John Doe',
        'time' => '2025-05-12 10:30 AM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Darren Smith',
        'speciality' => 'Cardiology',
        'patientName' => 'Maria Gonzales',
        'time' => '2025-05-13 02:15 PM',
        'status' => false,
    ],
    [
        'doctorName' => 'Dr. Linda Taylor',
        'speciality' => 'Neurology',
        'patientName' => 'Steve Brown',
        'time' => '2025-05-14 09:00 AM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Emily Stone',
        'speciality' => 'Orthopedics',
        'patientName' => 'Olivia Martinez',
        'time' => '2025-05-15 11:00 AM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Brian Clark',
        'speciality' => 'Pediatrics',
        'patientName' => 'Sophia Rodriguez',
        'time' => '2025-05-16 03:45 PM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Sarah Williams',
        'speciality' => 'Dermatology',
        'patientName' => 'James Wilson',
        'time' => '2025-05-17 01:30 PM',
        'status' => false,
    ],
    [
        'doctorName' => 'Dr. Thomas Harris',
        'speciality' => 'Psychiatry',
        'patientName' => 'Michael Brown',
        'time' => '2025-05-18 09:30 AM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Clara Johnson',
        'speciality' => 'Obstetrics',
        'patientName' => 'Sarah Davis',
        'time' => '2025-05-19 04:00 PM',
        'status' => true,
    ],
    [
        'doctorName' => 'Dr. Peter Green',
        'speciality' => 'Gastroenterology',
        'patientName' => 'David Garcia',
        'time' => '2025-05-20 10:15 AM',
        'status' => false,
    ],
    [
        'doctorName' => 'Dr. Alice Johnson',
        'speciality' => 'Cardiology',
        'patientName' => 'Emily Johnson',
        'time' => '2025-05-21 02:00 PM',
        'status' => true,
    ],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Doctor Dashboard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="../../css/admin/header.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/table.css" />
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
            <li class="nav-item"><a class="nav-link text-white" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="admin-doctors.php"><i class="fas fa-user-md"></i> Doctors</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="admin-patients.php"><i class="fas fa-users"></i> Patients</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="appointments.php"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="#"><i class="fas fa-cog"></i> Settings</a></li>
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
            <div class="container mt-5">
                <?php renderTable('appointments', $appointments); ?>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

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