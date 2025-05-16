<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Konekcija nije uspjela: " . $conn->connect_error);
}

// Use session ID or fallback to test ID
$patient_id = $_SESSION['user_id'] ?? 3003; // Default to test patient ID

// Get patient information
$query = "SELECT p.patient_id, u.username, u.email, p.date_of_birth, p.gender
          FROM patients p
          LEFT JOIN users u ON p.user_id = u.user_id 
          WHERE p.patient_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// If patient not found, use default values
if (!$patient) {
    $patient = [
        'patient_id' => 'N/A',
        'username' => 'Guest',
        'email' => 'Not available',
        'date_of_birth' => date('Y-m-d'),
        'gender' => 'Not specified'
    ];
    $error_message = "Patient data not found. Please ensure you're logged in correctly.";
}

// Get medical reports
$reports_query = "SELECT r.*, 
                 u.username as doctor_name,
                 d.specialization
                 FROM medical_reports r
                 JOIN doctors d ON r.doctor_id = d.doctor_id
                 JOIN users u ON d.doctor_id = u.user_id
                 WHERE r.patient_id = ?
                 ORDER BY r.report_date DESC";

$stmt = $conn->prepare($reports_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$medical_reports = $stmt->get_result();

// Get latest health parameters
$parameters_query = "SELECT * FROM patient_parameters 
                    WHERE patient_id = ? 
                    ORDER BY date_recorded DESC";

$stmt = $conn->prepare($parameters_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$parameters_result = $stmt->get_result();
$parameters_history = [];
while ($row = $parameters_result->fetch_assoc()) {
    $parameters_history[] = $row;
}

// Filter by date range if search parameters are provided
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$doctor_filter = isset($_GET['doctor']) ? $_GET['doctor'] : '';

$filtered_query = "SELECT r.*, 
                  u.username as doctor_name,
                  d.specialization
                  FROM medical_reports r
                  JOIN doctors d ON r.doctor_id = d.doctor_id
                  JOIN users u ON d.doctor_id = u.user_id
                  WHERE r.patient_id = ?";

$params = [$patient_id];
$types = "i";

if (!empty($date_from)) {
    $filtered_query .= " AND r.report_date >= ?";
    $params[] = $date_from;
    $types .= "s";
}

if (!empty($date_to)) {
    $filtered_query .= " AND r.report_date <= ?";
    $params[] = $date_to;
    $types .= "s";
}

if (!empty($doctor_filter)) {
    $filtered_query .= " AND u.username LIKE ?";
    $params[] = "%$doctor_filter%";
    $types .= "s";
}

$filtered_query .= " ORDER BY r.report_date DESC";

$stmt = $conn->prepare($filtered_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$filtered_reports = $stmt->get_result();

// Get list of doctors for filter dropdown
$doctors_query = "SELECT DISTINCT u.username, d.doctor_id
                 FROM users u
                 JOIN doctors d ON u.user_id = d.doctor_id
                 JOIN medical_reports r ON d.doctor_id = r.doctor_id
                 WHERE r.patient_id = ?
                 ORDER BY u.username";

$stmt = $conn->prepare($doctors_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$doctors_result = $stmt->get_result();
$doctors = [];
while ($row = $doctors_result->fetch_assoc()) {
    $doctors[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .health-parameter {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .health-parameter:last-child {
            border-bottom: none;
        }
        .report-card {
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .parameter-history {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Healthcare System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="appointments.php">Appointments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="medical_records.php">Medical Records</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="prescriptions.php">Prescriptions</a>
                </li>
            </ul>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($patient['username'] ?? 'Guest'); ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-lg-8">
            <h2><i class="bi bi-file-earmark-medical me-2"></i>Medical Records</h2>
            <p class="text-muted">View your complete medical history and health records</p>
        </div>
        <div class="col-lg-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#healthParametersModal">
                <i class="bi bi-heart-pulse me-2"></i>Health Parameter History
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Search & Filter Records</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-4">
                    <label for="doctor" class="form-label">Doctor</label>
                    <select class="form-select" id="doctor" name="doctor">
                        <option value="">All Doctors</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo htmlspecialchars($doctor['username']); ?>" <?php echo ($doctor_filter == $doctor['username']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                    <a href="medical_records.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Health Records & Reports -->
    <div class="row">
        <?php if ($filtered_reports->num_rows > 0): ?>
            <?php while ($report = $filtered_reports->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-clipboard2-pulse me-2"></i>Medical Report</span>
                                <span class="badge bg-primary">
                                    <?php echo date('M d, Y', strtotime($report['report_date'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($report['doctor_name'] ?? 'Unknown Doctor'); ?>
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($report['specialization'] ?? 'Specialist'); ?></span>
                            </h5>
                            <div class="mb-3">
                                <p><?php echo nl2br(htmlspecialchars($report['report_text'] ?? 'No details available')); ?></p>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#reportModal<?php echo $report['report_id']; ?>">
                                <i class="bi bi-eye me-2"></i>View Full Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Modal -->
                <div class="modal fade" id="reportModal<?php echo $report['report_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Medical Report Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <h5><?php echo htmlspecialchars($report['doctor_name'] ?? 'Unknown Doctor'); ?></h5>
                                        <p class="mb-0">
                                            <span class="badge bg-info text-dark"><?php echo htmlspecialchars($report['specialization'] ?? 'Specialist'); ?></span>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 fw-bold"><?php echo date('F d, Y', strtotime($report['report_date'])); ?></p>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Report Text</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php echo nl2br(htmlspecialchars($report['report_text'] ?? 'No report text available')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i>Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No medical records found matching your criteria. Please adjust your search or check back later.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Health Parameters History Modal -->
<div class="modal fade" id="healthParametersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Health Parameters History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (count($parameters_history) > 0): ?>
                    <div class="table-responsive parameter-history">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Weight (kg)</th>
                                <th>Height (cm)</th>
                                <th>Blood Pressure</th>
                                <th>Sugar Level (mg/dL)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($parameters_history as $param): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($param['date_recorded'])); ?></td>
                                    <td><?php echo $param['weight'] ?? 'N/A'; ?></td>
                                    <td><?php echo $param['height'] ?? 'N/A'; ?></td>
                                    <td><?php echo $param['blood_pressure'] ?? 'N/A'; ?></td>
                                    <td><?php echo $param['sugar_level'] ?? 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 2rem;"></i>
                        <p class="mt-3">No health parameter records found.</p>
                        <a href="add_parameters.php" class="btn btn-primary">Add Health Parameters</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="add_parameters.php" class="btn btn-primary">Update Parameters</a>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light text-center text-muted py-3 mt-5">
    <div class="container">
        <p class="mb-0">&copy; 2025 Healthcare System. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>