<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Charts</title>

    <!-- Bootstrap CSS & FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <!-- Morris.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin/header.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/layout.css" />
</head>
<style>
    #totalUsersChart, #reviewChart {
        cursor: pointer;
    }
    /* Style Morris chart elements */
    .morris-hover.morris-default-style {
        border-radius: 4px;
        padding: 6px 12px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .chart-container {
        max-width: 500px;
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 20px;
        border-radius: 5px;
        background-color: #fff;
    }
    #statusChart {
        height: 200px;
        width: 100%;
    }
</style>

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
        <main>

            <div class="container mt-4">
                <div class="row">
                    <!-- Status Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Status</div>
                            <div class="card-body">
                                <div id="statusChart" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Doctor Review -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Average Doctor Review</div>
                            <div class="card-body">
                                <div id="reviewChart" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<script>
    // Mock data for testing
    var mockData = [
        { year: '2020', facility_id: 1, total_appointments: 120 },
        { year: '2020', facility_id: 2, total_appointments: 80 },
        { year: '2021', facility_id: 1, total_appointments: 100 },
        { year: '2021', facility_id: 2, total_appointments: 70 },
        { year: '2022', facility_id: 1, total_appointments: 130 },
        { year: '2022', facility_id: 2, total_appointments: 90 },
        { year: '2023', facility_id: 1, total_appointments: 110 },
        { year: '2023', facility_id: 2, total_appointments: 75 },
    ];

    var linesData = [];
    var barData = [];

    // Process the mock data and organize it by year and facility
    mockData.forEach(function(item) {
        // For Line Chart: Group by year and facility_id
        var lineData = linesData.find(function(d) { return d.year === item.year && d.facility_id === item.facility_id; });
        if (lineData) {
            lineData.total_appointments += item.total_appointments;
        } else {
            linesData.push({
                year: item.year,
                facility_id: item.facility_id,
                total_appointments: item.total_appointments
            });
        }

        // For Bar Chart: Same structure, grouped by year and facility_id
        barData.push({
            year: item.year,
            facility_id: item.facility_id,
            total_appointments: item.total_appointments
        });
    });

    // Create the Line Chart
    Morris.Line({
        element: 'statusChart',
        data: linesData,
        xkey: 'year',
        ykeys: ['total_appointments'],
        labels: ['Total Appointments'],
        lineColors: ['#1e88e5', '#ff9800'], // Different colors for each facility
        xLabelAngle: 60,
        resize: true
    });

    // Create the Bar Chart
    Morris.Bar({
        element: 'reviewChart',
        data: barData,
        xkey: 'year',
        ykeys: ['total_appointments'],
        labels: ['Total Appointments'],
        barColors: ['#4caf50', '#f44336'], // Different colors for each facility
        xLabelAngle: 60,
        resize: true,
        ymax: 150 // Adjust the max value for the bar chart
    });
</script>

</body>
</html>
