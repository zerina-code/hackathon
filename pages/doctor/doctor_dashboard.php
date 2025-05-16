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

                    <!-- Total Patients & Doctors -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Total Patients & Doctors</div>
                            <div class="card-body">
                                <div id="totalUsersChart" style="height: 250px;"></div>
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

                    <!-- Revenue-->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Monthly Revenue Breakdown</div>
                            <div class="card-body">
                                <div id="revenueDonutChart" style="height: 250px;"></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    Morris.Line({
        element: 'statusChart',
        data: [
            { year: '2015', blue: 100, orange: 30 },
            { year: '2016', blue: 20, orange: 60 },
            { year: '2017', blue: 90, orange: 120 },
            { year: '2018', blue: 50, orange: 80 },
            { year: '2019', blue: 120, orange: 150 }
        ],
        xkey: 'year',
        ykeys: ['blue', 'orange'],
        labels: ['Blue Line', 'Orange Line'],
        lineColors: ['#1e88e5', '#ff9800'],
        pointSize: 4,
        pointStrokeColors: ['#1e88e5', '#ff9800'],
        pointFillColors: ['#fff', '#fff'],
        pointStrokeWidth: 2,
        lineWidth: 2,
        grid: true,
        gridTextColor: '#888',
        hideHover: 'auto',
        smooth: false,
        axes: true,
        resize: true,
        ymax: 200,
        ymin: 0
    });

    // Total Users (Bar)
    Morris.Bar({
        element: 'totalUsersChart',
        data: [
            { label: 'Patients', value: 320 },
            { label: 'Doctors', value: 45 }
        ],
        xkey: 'label',
        ykeys: ['value'],
        labels: ['Total'],
        barColors: ['#17a2b8'],
        resize: true,
        hideHover: 'auto',
        onclick: function(i, row) {
            alert(`You clicked on ${row.label} with a count of ${row.value}`);
        }
    });

    // Review Ratings (Bar)
    Morris.Bar({
        element: 'reviewChart',
        data: [
            { doctor: 'Dr. A', rating: 4.2 },
            { doctor: 'Dr. B', rating: 4.7 },
            { doctor: 'Dr. C', rating: 4.5 }
        ],
        xkey: 'doctor',
        ykeys: ['rating'],
        labels: ['Stars'],
        barColors: ['#ffc107'],
        resize: true,
        hideHover: 'auto',
        onclick: function(i, row) {
            alert(`Doctor ${row.doctor} has an average rating of ${row.rating} stars.`);
        }
    });
    Morris.Donut({
        element: 'revenueDonutChart',
        data: [
            { label: 'Q1', value: 19500 },
            { label: 'Q2', value: 30000 },
            { label: 'Q3', value: 31500 },
            { label: 'Q4', value: 39500 }
        ],
        colors: ['#4e73df', '#1cc88a', '#36b9cc', '#858796'],
        resize: true,
        formatter: function (value) {
            return '$' + value.toLocaleString();
        }
    });
</script>



</body>
</html>
