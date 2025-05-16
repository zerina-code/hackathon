<?php
include_once '../../components/table-component.php';
$patients = [
    [
        'name' => 'John Doe',
        'dob' => '1985-04-12',
        'address' => '123 Elm Street, Springfield',
        'phone' => '(123) 456-7890',
        'lastVisit' => '2025-05-10',
    ],
    [
        'name' => 'Jane Smith',
        'dob' => '1992-07-22',
        'address' => '456 Maple Avenue, Shelbyville',
        'phone' => '(987) 654-3210',
        'lastVisit' => '2025-04-28',
    ],
    [
        'name' => 'Emily Johnson',
        'dob' => '1990-03-15',
        'address' => '789 Oak Street, Capital City',
        'phone' => '(555) 123-4567',
        'lastVisit' => '2025-05-05',
    ],
    [
        'name' => 'Michael Brown',
        'dob' => '1980-11-30',
        'address' => '101 Pine Road, Springfield',
        'phone' => '(111) 222-3333',
        'lastVisit' => '2025-03-15',
    ],
    [
        'name' => 'Sarah Davis',
        'dob' => '1995-09-04',
        'address' => '202 Birch Lane, Shelbyville',
        'phone' => '(444) 555-6666',
        'lastVisit' => '2025-04-30',
    ],
    [
        'name' => 'James Wilson',
        'dob' => '1978-01-20',
        'address' => '303 Cedar Boulevard, Capital City',
        'phone' => '(777) 888-9999',
        'lastVisit' => '2025-02-12',
    ],
    [
        'name' => 'Olivia Martinez',
        'dob' => '2000-06-10',
        'address' => '404 Willow Avenue, Springfield',
        'phone' => '(333) 444-5555',
        'lastVisit' => '2025-05-02',
    ],
    [
        'name' => 'David Garcia',
        'dob' => '1983-12-05',
        'address' => '505 Fir Street, Shelbyville',
        'phone' => '(666) 777-8888',
        'lastVisit' => '2025-01-30',
    ],
    [
        'name' => 'Sophia Rodriguez',
        'dob' => '1993-08-19',
        'address' => '606 Chestnut Drive, Capital City',
        'phone' => '(888) 999-0000',
        'lastVisit' => '2025-04-25',
    ],
    [
        'name' => 'Benjamin Lee',
        'dob' => '1989-10-13',
        'address' => '707 Redwood Avenue, Springfield',
        'phone' => '(555) 777-8888',
        'lastVisit' => '2025-05-09',
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
                <?php renderTable('patients', $patients); ?>
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