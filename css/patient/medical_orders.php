<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set default patient ID for testing
$patient_id = $_SESSION['user_id'] ?? 3;

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

// Check if patient data was found
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

// No adding functionality - only doctors can create medical orders

// Get all medical orders for the patient
$orders_query = "SELECT mo.*, u.username as doctor_name 
                FROM medical_orders mo
                JOIN doctors d ON mo.doctor_id = d.doctor_id
                JOIN users u ON d.doctor_id = u.user_id
                WHERE mo.patient_id = ?
                ORDER BY mo.order_date DESC";

$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$medical_orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Orders | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        .medical-order {
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .order-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .order-doctor {
            font-weight: 500;
            color: #0d6efd;
        }
        .medical-order-text {
            margin-top: 10px;
            white-space: pre-line;
        }
        .no-orders {
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 4px;
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
                    <a class="nav-link" href="medical_records.php">Medical Records</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="prescriptions.php">Prescriptions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="medical_orders.php">Medical Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="diagnostic_procedures.php">Diagnostic Procedures</a>
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
        <div class="col-md-8">
            <h2><i class="bi bi-clipboard2-pulse me-2"></i>Medical Orders</h2>
            <p class="text-muted">View your medical orders, referrals, and test requisitions</p>
        </div>
        <div class="col-md-4 text-end">
            <!-- No add button since only doctors can create orders -->
            <a href="diagnostic_procedures.php" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-medical me-1"></i> Schedule Diagnostic Procedures
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Medical Orders</h5>
                </div>
                <div class="card-body">
                    <?php if ($medical_orders->num_rows > 0): ?>
                        <?php while($order = $medical_orders->fetch_assoc()): ?>
                            <div class="medical-order">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="order-doctor">Dr. <?php echo htmlspecialchars($order['doctor_name']); ?></span>
                                    </div>
                                    <div class="order-date">
                                        <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                                    </div>
                                </div>
                                <div class="medical-order-text">
                                    <?php echo nl2br(htmlspecialchars($order['order_text'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-orders">
                            <i class="bi bi-clipboard2-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h4 class="mt-3">No Medical Orders Found</h4>
                            <p class="text-muted">You currently have no medical orders in your records.</p>
                            <p class="text-muted">Medical orders are created by doctors during appointments.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove the Add Medical Order Modal as patients should not add their own orders -->

<footer class="bg-light text-center text-muted py-3 mt-5">
    <div class="container">
        <p class="mb-0">&copy; 2025 Healthcare System. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>