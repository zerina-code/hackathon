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

// Get patient ID from session or default to test ID
$patient_id = $_SESSION['user_id'] ?? 3; // Default to 3 for testing

// Get patient basic information
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
    // Handle the error - either redirect to login or use default values
    $patient = [
        'patient_id' => 'N/A',
        'username' => 'Guest',
        'email' => 'Not available',
        'date_of_birth' => date('Y-m-d'),
        'gender' => 'Not specified'
    ];

    $error_message = "Patient data not found. Please ensure you're logged in correctly.";
}

// Get current prescriptions
$current_prescriptions_query = "SELECT p.prescription_id, p.prescription_date, 
                               m.medication_name, m.dosage, m.currently_used,
                               u.username as doctor_name,
                               d.specialization
                               FROM prescriptions p
                               JOIN medications m ON p.prescription_id = m.prescription_id
                               JOIN doctors d ON p.doctor_id = d.doctor_id
                               JOIN users u ON d.doctor_id = u.user_id
                               WHERE p.patient_id = ? AND m.currently_used = 1
                               ORDER BY p.prescription_date DESC";

$stmt = $conn->prepare($current_prescriptions_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$current_prescriptions = $stmt->get_result();

// Get prescription history
$history_prescriptions_query = "SELECT p.prescription_id, p.prescription_date, 
                               m.medication_name, m.dosage, m.currently_used,
                               u.username as doctor_name,
                               d.specialization
                               FROM prescriptions p
                               JOIN medications m ON p.prescription_id = m.prescription_id
                               JOIN doctors d ON p.doctor_id = d.doctor_id
                               JOIN users u ON d.doctor_id = u.user_id
                               WHERE p.patient_id = ? AND (m.currently_used = 0 OR m.currently_used IS NULL)
                               ORDER BY p.prescription_date DESC";

$stmt = $conn->prepare($history_prescriptions_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$history_prescriptions = $stmt->get_result();

// Handle prescription filtering if search form is submitted
$search_term = '';
$filter_active = false;

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $filter_active = true;

    // Search query for current medications
    $current_search_query = "SELECT p.prescription_id, p.prescription_date, 
                           m.medication_name, m.dosage, m.currently_used,
                           u.username as doctor_name,
                           d.specialization
                           FROM prescriptions p
                           JOIN medications m ON p.prescription_id = m.prescription_id
                           JOIN doctors d ON p.doctor_id = d.doctor_id
                           JOIN users u ON d.doctor_id = u.user_id
                           WHERE p.patient_id = ? 
                           AND m.currently_used = 1
                           AND (m.medication_name LIKE ? OR u.username LIKE ?)
                           ORDER BY p.prescription_date DESC";

    $stmt = $conn->prepare($current_search_query);
    $search_param = "%" . $search_term . "%";
    $stmt->bind_param("iss", $patient_id, $search_param, $search_param);
    $stmt->execute();
    $current_prescriptions = $stmt->get_result();

    // Search query for prescription history
    $history_search_query = "SELECT p.prescription_id, p.prescription_date, 
                           m.medication_name, m.dosage, m.currently_used,
                           u.username as doctor_name,
                           d.specialization
                           FROM prescriptions p
                           JOIN medications m ON p.prescription_id = m.prescription_id
                           JOIN doctors d ON p.doctor_id = d.doctor_id
                           JOIN users u ON d.doctor_id = u.user_id
                           WHERE p.patient_id = ? 
                           AND (m.currently_used = 0 OR m.currently_used IS NULL)
                           AND (m.medication_name LIKE ? OR u.username LIKE ?)
                           ORDER BY p.prescription_date DESC";

    $stmt = $conn->prepare($history_search_query);
    $stmt->bind_param("iss", $patient_id, $search_param, $search_param);
    $stmt->execute();
    $history_prescriptions = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .prescription-card {
            border-left: 4px solid #dc3545;
            transition: all 0.3s;
        }
        .prescription-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .prescription-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .prescription-item:last-child {
            border-bottom: none;
        }
        .medication-name {
            font-weight: 600;
            color: #dc3545;
        }
        .prescription-badge {
            font-size: 0.8rem;
        }
        .prescription-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .doctor-info {
            font-size: 0.85rem;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #dc3545;
            font-weight: 600;
        }
        .filter-badge {
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
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
                    <a class="nav-link active" href="prescriptions.php">Prescriptions</a>
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
        <div class="col">
            <h2><i class="bi bi-capsule me-2 text-danger"></i>My Prescriptions</h2>
            <p class="text-muted">Manage and view your medication prescriptions</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search for medication or doctor" name="search" value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>

            <?php if ($filter_active): ?>
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <div class="me-2">Active filters:</div>
                        <span class="filter-badge bg-light text-dark">
                        Search: <?php echo htmlspecialchars($search_term); ?>
                        <a href="prescriptions.php" class="ms-2 text-decoration-none"><i class="bi bi-x-circle"></i></a>
                    </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Prescriptions Content -->
    <div class="card">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="prescriptions-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab" aria-controls="current" aria-selected="true">
                        Current Medications
                        <span class="badge bg-danger ms-2"><?php echo $current_prescriptions->num_rows; ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                        Medication History
                        <span class="badge bg-secondary ms-2"><?php echo $history_prescriptions->num_rows; ?></span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="prescriptions-content">
                <!-- Current Medications Tab -->
                <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
                    <?php if ($current_prescriptions->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($prescription = $current_prescriptions->fetch_assoc()): ?>
                                <div class="list-group-item prescription-item">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <h5 class="medication-name mb-0"><?php echo htmlspecialchars($prescription['medication_name']); ?></h5>
                                                <span class="badge bg-success ms-2 prescription-badge">Active</span>
                                            </div>
                                            <?php if (!empty($prescription['dosage'])): ?>
                                                <p class="mb-1"><strong>Dosage:</strong> <?php echo htmlspecialchars($prescription['dosage']); ?></p>
                                            <?php endif; ?>
                                            <div class="doctor-info">
                                                <i class="bi bi-person-badge me-1"></i>
                                                <strong>Prescribed by:</strong> <?php echo htmlspecialchars($prescription['doctor_name']); ?>
                                                <?php if (!empty($prescription['specialization'])): ?>
                                                    <span class="text-muted">(<?php echo htmlspecialchars($prescription['specialization']); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <p class="prescription-date mb-1">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                <?php echo date('F d, Y', strtotime($prescription['prescription_date'])); ?>
                                            </p>
                                            <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#prescriptionModal<?php echo $prescription['prescription_id']; ?>">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prescription Modal -->
                                <div class="modal fade" id="prescriptionModal<?php echo $prescription['prescription_id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Prescription Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <i class="bi bi-capsule-pill text-danger" style="font-size: 3rem;"></i>
                                                    <h4 class="mt-2"><?php echo htmlspecialchars($prescription['medication_name']); ?></h4>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Prescription Date</label>
                                                        <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($prescription['prescription_date'])); ?>" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Status</label>
                                                        <input type="text" class="form-control" value="Active" readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Dosage</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($prescription['dosage'] ?? 'Not specified'); ?>" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Prescribed By</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($prescription['doctor_name']); ?> (<?php echo htmlspecialchars($prescription['specialization'] ?? 'Not specified'); ?>)" readonly>
                                                </div>

                                                <!-- Additional information could be fetched from the DB if needed -->
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    Always follow your doctor's instructions regarding medication usage.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <!-- Add print option or other actions if needed -->
                                                <button type="button" class="btn btn-primary" onclick="window.print();">Print</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No Current Medications</h5>
                            <p class="text-muted">You don't have any active prescriptions at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Medication History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <?php if ($history_prescriptions->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($prescription = $history_prescriptions->fetch_assoc()): ?>
                                <div class="list-group-item prescription-item">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <h5 class="medication-name mb-0"><?php echo htmlspecialchars($prescription['medication_name']); ?></h5>
                                                <span class="badge bg-secondary ms-2 prescription-badge">Inactive</span>
                                            </div>
                                            <?php if (!empty($prescription['dosage'])): ?>
                                                <p class="mb-1"><strong>Dosage:</strong> <?php echo htmlspecialchars($prescription['dosage']); ?></p>
                                            <?php endif; ?>
                                            <div class="doctor-info">
                                                <i class="bi bi-person-badge me-1"></i>
                                                <strong>Prescribed by:</strong> <?php echo htmlspecialchars($prescription['doctor_name']); ?>
                                                <?php if (!empty($prescription['specialization'])): ?>
                                                    <span class="text-muted">(<?php echo htmlspecialchars($prescription['specialization']); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <p class="prescription-date mb-1">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                <?php echo date('F d, Y', strtotime($prescription['prescription_date'])); ?>
                                            </p>
                                            <button class="btn btn-sm btn-outline-secondary mt-2" data-bs-toggle="modal" data-bs-target="#historyModal<?php echo $prescription['prescription_id']; ?>">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- History Prescription Modal -->
                                <div class="modal fade" id="historyModal<?php echo $prescription['prescription_id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Prescription History</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <i class="bi bi-capsule-pill text-secondary" style="font-size: 3rem;"></i>
                                                    <h4 class="mt-2"><?php echo htmlspecialchars($prescription['medication_name']); ?></h4>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Prescription Date</label>
                                                        <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($prescription['prescription_date'])); ?>" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Status</label>
                                                        <input type="text" class="form-control" value="Inactive" readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Dosage</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($prescription['dosage'] ?? 'Not specified'); ?>" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Prescribed By</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($prescription['doctor_name']); ?> (<?php echo htmlspecialchars($prescription['specialization'] ?? 'Not specified'); ?>)" readonly>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" onclick="window.print();">Print</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No Medication History</h5>
                            <p class="text-muted">Your past prescription records will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Medication Reminders Section -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-alarm me-2"></i>Medication Reminders</h5>
                <a href="add_reminder.php" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add Reminder
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- This section would ideally fetch reminders related to medications -->
            <div class="alert alert-info mb-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">Reminder Service</h6>
                        <p class="mb-0">Set up medication reminders to help you stay on track with your prescriptions. Never miss a dose!</p>
                    </div>
                </div>
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