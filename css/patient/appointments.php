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

// Handle appointment cancellation if cancel_id is set
if (isset($_POST['cancel_id']) && !empty($_POST['cancel_id'])) {
    $cancel_id = $_POST['cancel_id'];

    // Delete the appointment
    $delete_query = "DELETE FROM appointments WHERE appointment_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $cancel_id);

    if ($stmt->execute()) {
        $success_message = "Appointment successfully cancelled.";
    } else {
        $error_message = "Error cancelling appointment: " . $conn->error;
    }
    $stmt->close();
}

// In a real application, you'd check if the user is logged in
// For development, we'll use a test patient ID
$patient_id = $_SESSION['user_id'] ?? 3002; // Default to 3002 for testing

// Get patient basic information
$query = "SELECT p.patient_id, u.username, u.email, p.date_of_birth, p.gender
          FROM patients p
          LEFT JOIN users u ON p.patient_id = u.user_id 
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
        'patient_id' => $patient_id,
        'username' => 'Guest',
        'email' => 'Not available',
        'date_of_birth' => date('Y-m-d'),
        'gender' => 'Not specified'
    ];
    $error_message = "Patient data not found. Please ensure you're logged in correctly.";
}

// Get all appointments (both upcoming and past)
$appointments_query = "SELECT a.*, 
                      u.username as doctor_name, 
                      d.specialization as doctor_specialization,
                      f.name as facility_name 
                      FROM appointments a 
                      JOIN doctors d ON a.doctor_id = d.doctor_id 
                      JOIN users u ON d.doctor_id = u.user_id
                      JOIN facilities f ON a.facility_id = f.facility_id
                      WHERE a.patient_id = ? 
                      ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$all_appointments = $stmt->get_result();

// Separate upcoming and past appointments
$upcoming_appointments = [];
$past_appointments = [];
$current_date = date('Y-m-d H:i:s');

while ($appointment = $all_appointments->fetch_assoc()) {
    if ($appointment['appointment_date'] >= $current_date) {
        $upcoming_appointments[] = $appointment;
    } else {
        $past_appointments[] = $appointment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        .appointment-card {
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-upcoming {
            background-color: #28a745;
        }
        .status-past {
            background-color: #6c757d;
        }
        .tab-content {
            padding-top: 20px;
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
                    <a class="nav-link active" href="appointments.php">Appointments</a>
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
                <a href="/hackathon/php/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-event me-2"></i>My Appointments</h2>
        <a href="make_appointment.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>New Appointment
        </a>
    </div>

    <!-- Tabs for Upcoming and Past appointments -->
    <ul class="nav nav-tabs" id="appointmentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                <i class="bi bi-calendar-check me-1"></i>Upcoming (<?php echo count($upcoming_appointments); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
                <i class="bi bi-calendar-x me-1"></i>Past (<?php echo count($past_appointments); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="appointmentTabsContent">
        <!-- Upcoming Appointments Tab -->
        <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
            <?php if (count($upcoming_appointments) > 0): ?>
                <div class="row">
                    <?php foreach($upcoming_appointments as $appointment): ?>
                        <div class="col-md-6">
                            <div class="card appointment-card">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <span class="status-indicator status-upcoming"></span>
                                            <?php echo date('D, M d, Y', strtotime($appointment['appointment_date'])); ?>
                                        </h5>
                                        <span class="badge bg-light text-primary">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h5>
                                    <p class="card-text">
                                        <i class="bi bi-star me-1"></i> <?php echo htmlspecialchars($appointment['doctor_specialization']); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($appointment['facility_name']); ?>
                                    </p>
                                    <?php if (!empty($appointment['reason'])): ?>
                                        <p class="card-text">
                                            <strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($appointment['status'])): ?>
                                        <p class="mb-0">
                                            <span class="badge bg-info"><?php echo htmlspecialchars($appointment['status']); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-between">
                                        <form method="post" onsubmit="return confirmCancel();">
                                            <input type="hidden" name="cancel_id" value="<?php echo $appointment['appointment_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle me-1"></i>Cancel
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-primary" onclick="rescheduleAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                            <i class="bi bi-calendar-plus me-1"></i>Reschedule
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center my-4" role="alert">
                    <i class="bi bi-info-circle me-2"></i> You don't have any upcoming appointments.
                    <div class="mt-3">
                        <a href="make_appointment.php" class="btn btn-primary">Schedule New Appointment</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Past Appointments Tab -->
        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
            <?php if (count($past_appointments) > 0): ?>
                <div class="row">
                    <?php foreach($past_appointments as $appointment): ?>
                        <div class="col-md-6">
                            <div class="card appointment-card">
                                <div class="card-header bg-secondary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <span class="status-indicator status-past"></span>
                                            <?php echo date('D, M d, Y', strtotime($appointment['appointment_date'])); ?>
                                        </h5>
                                        <span class="badge bg-light text-secondary">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h5>
                                    <p class="card-text">
                                        <i class="bi bi-star me-1"></i> <?php echo htmlspecialchars($appointment['doctor_specialization']); ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($appointment['facility_name']); ?>
                                    </p>
                                    <?php if (!empty($appointment['reason'])): ?>
                                        <p class="card-text">
                                            <strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($appointment['status'])): ?>
                                        <p class="mb-0">
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($appointment['status']); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-text me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center my-4" role="alert">
                    <i class="bi bi-info-circle me-2"></i> You don't have any past appointments.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="bg-light text-center text-muted py-3 mt-5">
    <div class="container">
        <p class="mb-0">&copy; 2025 Healthcare System. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Existing script
    function confirmCancel() {
        return confirm('Are you sure you want to cancel this appointment?');
    }

    // Updated reschedule function
    function rescheduleAppointment(appointmentId) {
        // Redirect to the reschedule page with the appointment ID
        window.location.href = 'reschedule.php?id=' + appointmentId;
    }
</script>
</body>
</html>