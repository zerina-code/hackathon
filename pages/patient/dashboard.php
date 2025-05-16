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

// Ako je konekcija uspješna
// echo "Uspješno povezano s bazom podataka!";

// In a real application, you'd check if the user is logged in
// For development, we'll use a test patient ID
// Change this to match your patient_id (e.g., 3 based on your data)
$patient_id = $_SESSION['user_id'] ?? 3; // Default to 3 for testing, based on your data

// Get patient basic information - let's modify the query to better handle the relationship
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

    // You might want to add a message
    $error_message = "Patient data not found. Please ensure you're logged in correctly.";
}

// Get upcoming appointments
// Remember doctor_id in the doctors table is the foreign key to user_id in users table
$appointments_query = "SELECT a.*, 
                      u.username as doctor_name, 
                      f.name as facility_name 
                      FROM appointments a 
                      JOIN doctors d ON a.doctor_id = d.doctor_id 
                      JOIN users u ON d.doctor_id = u.user_id
                      JOIN facilities f ON a.facility_id = f.facility_id
                      WHERE a.patient_id = ? 
                      AND a.appointment_date >= NOW() 
                      ORDER BY a.appointment_date ASC 
                      LIMIT 5";

$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$upcoming_appointments = $stmt->get_result();

// Get recent prescriptions - modified to match existing schema
$prescriptions_query = "SELECT p.*, 
                        m.medication_name,
                        u.username as doctor_name
                        FROM prescriptions p
                        JOIN medications m ON p.prescription_id = m.prescription_id
                        JOIN doctors d ON p.doctor_id = d.doctor_id
                        JOIN users u ON d.doctor_id = u.user_id
                        WHERE p.patient_id = ?
                        ORDER BY p.prescription_date DESC
                        LIMIT 3";

$stmt = $conn->prepare($prescriptions_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$recent_prescriptions = $stmt->get_result();

// Get recent reminders
$reminders_query = "SELECT * FROM reminders 
                   WHERE patient_id = ? 
                   AND reminder_date >= NOW() 
                   ORDER BY reminder_date ASC 
                   LIMIT 3";

$stmt = $conn->prepare($reminders_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$reminders = $stmt->get_result();

// Get patient parameters (most recent)
$parameters_query = "SELECT * FROM patient_parameters 
                    WHERE patient_id = ? 
                    ORDER BY date_recorded DESC 
                    LIMIT 1";

$stmt = $conn->prepare($parameters_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$parameters = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
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
                    <a class="nav-link active" href="dashboard.php">Dashboard</a>
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
            <h2>Welcome, <?php echo htmlspecialchars($patient['username'] ?? 'Guest'); ?>!</h2>
            <!-- Removed insurance badge since that column doesn't exist -->
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="bi bi-person"></i> View Profile
            </button>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="dashboard-card card text-center p-3">
                <div class="card-body">
                    <div class="card-icon text-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h5 class="card-title">Appointments</h5>
                    <p class="card-text">Schedule or manage your appointments.</p>
                    <a href="make_appointment.php" class="btn btn-primary mt-2">Book Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card card text-center p-3">
                <div class="card-body">
                    <div class="card-icon text-danger">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <h5 class="card-title">Prescriptions</h5>
                    <p class="card-text">Access your medication prescriptions.</p>
                    <a href="prescriptions.php" class="btn btn-danger mt-2">View All</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card card text-center p-3">
                <div class="card-body">
                    <div class="card-icon text-success">
                        <i class="bi bi-file-earmark-medical"></i>
                    </div>
                    <h5 class="card-title">Medical Records</h5>
                    <p class="card-text">View your medical history and reports.</p>
                    <a href="medical_records.php" class="btn btn-success mt-2">Access</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="dashboard-card card text-center p-3">
                <div class="card-body">
                    <div class="card-icon text-warning">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <h5 class="card-title">Medical Orders</h5>
                    <p class="card-text">View your medical orders and referrals.</p>
                    <a href="medical_orders.php" class="btn btn-warning mt-2 text-dark">View</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Upcoming Appointments -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Upcoming Appointments</h5>
                    <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if ($upcoming_appointments->num_rows > 0): ?>
                        <?php while($appointment = $upcoming_appointments->fetch_assoc()): ?>
                            <div class="appointment-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?php echo htmlspecialchars($appointment['doctor_name'] ?? 'Unknown Doctor'); ?></h6>
                                        <p class="mb-0 text-muted">
                                            <i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($appointment['facility_name'] ?? 'Unknown Facility'); ?>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <strong>
                                            <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?>
                                        </strong>
                                        <p class="mb-0">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php if (!empty($appointment['reason'])): ?>
                                    <p class="mt-2 mb-0"><small>Reason: <?php echo htmlspecialchars($appointment['reason']); ?></small></p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center p-3">
                            <p class="mb-0">No upcoming appointments.</p>
                            <a href="make_appointment.php" class="btn btn-sm btn-primary mt-2">Schedule New Appointment</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Prescriptions -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-medical me-2"></i>Recent Prescriptions</h5>
                    <a href="prescriptions.php" class="btn btn-sm btn-outline-danger">View All</a>
                </div>
                <div class="card-body">
                    <?php if ($recent_prescriptions->num_rows > 0): ?>
                        <?php while($prescription = $recent_prescriptions->fetch_assoc()): ?>
                            <div class="prescription-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?php echo htmlspecialchars($prescription['medication_name'] ?? 'Unknown Medication'); ?></h6>
                                        <!-- Adjusting to match the schema -->
                                        <p class="mb-0">
                                            <?php if (!empty($prescription['dosage'])): ?>
                                                Dosage: <?php echo htmlspecialchars($prescription['dosage']); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted">Prescribed by: <?php echo htmlspecialchars($prescription['doctor_name'] ?? 'Unknown Doctor'); ?></span>
                                        <p class="mb-0 text-muted">
                                            <?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center p-3">No recent prescriptions.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Health Parameters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Health Parameters</h5>
                </div>
                <div class="card-body">
                    <?php if ($parameters): ?>
                        <div class="health-parameter">
                            <small class="text-muted">Weight</small>
                            <h5><?php echo $parameters['weight']; ?> kg</h5>
                        </div>
                        <div class="health-parameter">
                            <small class="text-muted">Height</small>
                            <h5><?php echo $parameters['height']; ?> cm</h5>
                        </div>
                        <div class="health-parameter">
                            <small class="text-muted">Blood Pressure</small>
                            <h5><?php echo $parameters['blood_pressure']; ?> mmHg</h5>
                        </div>
                        <div class="health-parameter">
                            <small class="text-muted">Sugar Level</small>
                            <h5><?php echo $parameters['sugar_level']; ?> mg/dL</h5>
                        </div>
                        <div class="text-muted mt-2 text-center">
                            <small>Last updated: <?php echo date('M d, Y', strtotime($parameters['date_recorded'])); ?></small>
                        </div>
                    <?php else: ?>
                        <p class="text-center py-3">No health parameters recorded yet.</p>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="add_parameters.php" class="btn btn-sm btn-outline-primary">Update Parameters</a>
                    </div>
                </div>
            </div>

            <!-- Reminders -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Reminders</h5>
                    <a href="reminders.php" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>
                <div class="card-body">
                    <?php if ($reminders->num_rows > 0): ?>
                        <?php while($reminder = $reminders->fetch_assoc()): ?>
                            <div class="reminder-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?php echo htmlspecialchars($reminder['reminder_text'] ?? 'No text'); ?></h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($reminder['type'] ?? 'General'); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($reminder['reminder_date'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center py-3">No upcoming reminders.</p>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="add_reminder.php" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add Reminder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle" style="font-size: 4rem;"></i>
                    <h4 class="mt-2"><?php echo htmlspecialchars($patient['username'] ?? 'Guest'); ?></h4>
                    <p class="text-muted">Patient ID: <?php echo $patient['patient_id'] ?? 'N/A'; ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['email'] ?? 'Not available'); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="text" class="form-control" value="<?php echo isset($patient['date_of_birth']) ? date('F d, Y', strtotime($patient['date_of_birth'])) : 'Not available'; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($patient['gender'] ?? 'Not specified'); ?>" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
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