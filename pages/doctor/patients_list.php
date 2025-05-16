<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/doctor/doctor.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/layout.css" />
</head>
<body>
<div class="main-wrapper d-flex">
    <!-- Sidebar -->
    <nav class="sidebar bg-dark text-white p-3">
        <div class="sidebar-header mb-4">
            <a href="#" class="navbar-brand text-white">
                <img src="../../assets/logo.png" alt="logo" style="height: 60px;">

            </a>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="doctor_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="patients_list.php">Patients</a></li>
            <li class="nav-item"><a class="nav-link" href="add_diagnostic_procedure.php">Add Procedure</a></li>
            <li class="nav-item"><a class="nav-link" href="appointment_approve.php">Appointments</a></li>
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
            <h2 class="mb-3">Patients</h2>

            <!-- Facility Filter Dropdown -->
            <div class="mb-3">
                <label for="facilityFilter" class="form-label">Filter by Facility:</label>
                <select id="facilityFilter" class="form-select w-auto d-inline-block">
                    <option value="">All Facilities</option>
                    <option value="Springfield Medical Center">Springfield Medical Center</option>
                    <option value="Riverside Health Clinic">Riverside Health Clinic</option>
                </select>
            </div>

            <!-- Patient Table -->
            <table id="patientTable" class="table table-bordered table-hover">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Last Visit</th>
                    <th>Facility</th>
                    <th>View Procedures</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>1985-04-12</td>
                    <td>123 Elm Street, Springfield</td>
                    <td>(123) 456-7890</td>
                    <td>2025-05-10</td>
                    <td>Springfield Medical Center</td>
                    <td><a href="add_diagnostic_procedure.php?patient_id=1" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>1990-07-20</td>
                    <td>456 Oak Avenue, Riverside</td>
                    <td>(321) 654-0987</td>
                    <td>2025-05-12</td>
                    <td>Riverside Health Clinic</td>
                    <td><a href="add_diagnostic_procedure.php?patient_id=2" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                </tbody>
            </table>
        </main>
    </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#patientTable').DataTable();

        // --- Auto-populate unique facilities ---
        const facilitySet = new Set();
        $('#patientTable tbody tr').each(function () {
            const facility = $(this).find('td:eq(5)').text().trim();
            if (facility) facilitySet.add(facility);
        });

        // --- Populate the dropdown ---
        const $filter = $('#facilityFilter');
        $filter.empty();
        $filter.append('<option value="">All Facilities</option>');
        Array.from(facilitySet).sort().forEach(facility => {
            $filter.append(`<option value="${facility}">${facility}</option>`);
        });

        // --- Filtering logic ---
        $filter.on('change', function () {
            const selected = this.value.toLowerCase();

            table.rows().every(function () {
                const row = this.node();
                const facility = $(row).find('td:eq(5)').text().toLowerCase();

                if (!selected || facility === selected) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        });
    });
</script>


</body>
</html>


