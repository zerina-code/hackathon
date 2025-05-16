<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Charts</title>

    <!-- Bootstrap & Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/doctor/doctor.css" />
    <link rel="stylesheet" href="../../css/admin/sidebar.css" />
    <link rel="stylesheet" href="../../css/admin/layout.css" />

    <style>
        #totalUsersChart, #reviewChart, #diagnosisChart {
            cursor: pointer;
        }

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
            <li class="nav-item"><a class="nav-link" href="patients_list.php">Patients</a></li>
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

                    <!-- Total Users Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Total Patients & Doctors</div>
                            <div class="card-body">
                                <div id="totalUsersChart" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Average Doctor Review</div>
                            <div class="card-body">
                                <div id="reviewChart" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Monthly Revenue Breakdown</div>
                            <div class="card-body">
                                <div id="revenueDonutChart" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Most Common Diagnoses Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Most Common Diagnoses</div>
                            <div class="card-body">
                                <div id="diagnosisChart" style="height: 250px;"></div>
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
        hideHover: 'auto'
    });

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
        hideHover: 'auto'
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

    // ✅ Most Common Diagnoses Chart
    Morris.Bar({
        element: 'diagnosisChart',
        data: [
            { diagnosis: 'Flu', count: 85 },
            { diagnosis: 'COVID-19', count: 60 },
            { diagnosis: 'Diabetes', count: 45 },
            { diagnosis: 'Hypertension', count: 35 },
            { diagnosis: 'Allergies', count: 28 }
        ],
        xkey: 'diagnosis',
        ykeys: ['count'],
        labels: ['Cases'],
        barColors: ['#8E7CC3'],
        resize: true,
        hideHover: 'auto'
    });
</script>

</body>
</html>
