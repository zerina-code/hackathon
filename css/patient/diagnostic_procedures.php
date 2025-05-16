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

// Set default patient ID for testing or get from session
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

// Get all active medical orders (referrals) that can be scheduled
$orders_query = "SELECT mo.order_id, mo.order_text, mo.order_date, u.username as doctor_name, d.specialization
                 FROM medical_orders mo
                 JOIN doctors d ON mo.doctor_id = d.doctor_id
                 JOIN users u ON d.doctor_id = u.user_id
                 WHERE mo.patient_id = ? 
                 AND (mo.order_text LIKE '%MRI%' 
                     OR mo.order_text LIKE '%CT%' 
                     OR mo.order_text LIKE '%X-Ray%'
                     OR mo.order_text LIKE '%Ultrasound%'
                     OR mo.order_text LIKE '%Lab%'
                     OR mo.order_text LIKE '%Test%'
                     OR mo.order_text LIKE '%Scan%')
                 AND mo.order_text NOT LIKE '%APPOINTMENT REQUESTED%'
                 ORDER BY mo.order_date DESC";

$stmt = $conn->prepare($orders_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$available_orders = $stmt->get_result();

// Process form submission
$success_message = null;
$error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    $order_id = $_POST['order_id'];
    $preferred_date = $_POST['preferred_date'] ?? '';
    $preferred_time = $_POST['preferred_time'] ?? '';
    $additional_notes = $_POST['additional_notes'] ?? '';
    $current_date = date('Y-m-d');

    // Get the original order information
    $get_order = "SELECT mo.*, u.username as doctor_name 
                  FROM medical_orders mo 
                  JOIN doctors d ON mo.doctor_id = d.doctor_id
                  JOIN users u ON d.doctor_id = u.user_id
                  WHERE mo.order_id = ? AND mo.patient_id = ?";
    $stmt = $conn->prepare($get_order);
    $stmt->bind_param("ii", $order_id, $patient_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();

        // Update the order text to indicate an appointment has been requested
        $updated_text = $order['order_text'] . "\n\n--- APPOINTMENT REQUESTED ---";
        if (!empty($preferred_date)) {
            $updated_text .= "\nPreferred Date: " . date('F d, Y', strtotime($preferred_date));
        }
        if (!empty($preferred_time)) {
            $updated_text .= "\nPreferred Time: " . $preferred_time;
        }
        if (!empty($additional_notes)) {
            $updated_text .= "\nPatient Notes: " . $additional_notes;
        }
        $updated_text .= "\nRequest Date: " . date('F d, Y');
        $updated_text .= "\n[SCHEDULING PENDING]";

        // Update the medical order
        $update_query = "UPDATE medical_orders SET order_text = ? WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $updated_text, $order_id);

        if ($stmt->execute()) {
            $success_message = "Your appointment request for the diagnostic procedure has been submitted. The facility will contact you with your scheduled time.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    } else {
        $error_message = "Invalid medical order selected.";
    }
}

// Get all orders with appointment requests
$scheduling_query = "SELECT mo.*, u.username as doctor_name 
                     FROM medical_orders mo
                     JOIN doctors d ON mo.doctor_id = d.doctor_id
                     JOIN users u ON d.doctor_id = u.user_id
                     WHERE mo.patient_id = ? 
                     AND mo.order_text LIKE '%APPOINTMENT REQUESTED%'
                     ORDER BY mo.order_date DESC";

$stmt = $conn->prepare($scheduling_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$scheduled_procedures = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Procedures | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <style>
        .procedure-request {
            border-left: 4px solid #20c997;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .request-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .request-doctor {
            font-weight: 500;
            color: #0d6efd;
        }
        .request-text {
            margin-top: 10px;
            white-space: pre-line;
        }
        .no-requests {
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .medical-order {
            border-left: 4px solid #6f42c1;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .medical-order:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .order-text {
            margin-top: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .order-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .status-pending {
            padding: 3px 10px;
            background-color: #ffc107;
            color: #212529;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-scheduled {
            padding: 3px 10px;
            background-color: #28a745;
            color: #fff;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-rejected {
            padding: 3px 10px;
            background-color: #dc3545;
            color: #fff;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-completed {
            padding: 3px 10px;
            background-color: #6c757d;
            color: #fff;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .tab-pane {
            padding: 20px 0;
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
                    <a class="nav-link" href="medical_orders.php">Medical Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="diagnostic_procedures.php">Diagnostic Procedures</a>
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
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic Procedures</h2>
            <p class="text-muted">Schedule appointments for diagnostic procedures based on your medical orders and referrals</p>
        </div>
    </div>

    <ul class="nav nav-tabs" id="procedureTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="available-tab" data-bs-toggle="tab" data-bs-target="#available-procedures" type="button" role="tab" aria-controls="available-procedures" aria-selected="true">
                Available Referrals
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="scheduled-tab" data-bs-toggle="tab" data-bs-target="#scheduled-procedures" type="button" role="tab" aria-controls="scheduled-procedures" aria-selected="false">
                Scheduled Procedures
            </button>
        </li>
    </ul>

    <div class="tab-content" id="procedureTabsContent">
        <div class="tab-pane fade show active" id="available-procedures" role="tabpanel" aria-labelledby="available-tab">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body">
                    <h5 class="card-title">Medical Orders Available for Scheduling</h5>
                    <p class="text-muted">Click on a referral to request an appointment</p>

                    <?php if ($available_orders->num_rows > 0): ?>
                        <div class="row">
                            <?php while($order = $available_orders->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="medical-order" data-bs-toggle="modal" data-bs-target="#scheduleModal"
                                         data-order-id="<?php echo $order['order_id']; ?>"
                                         data-order-text="<?php echo htmlspecialchars($order['order_text']); ?>"
                                         data-doctor="<?php echo htmlspecialchars($order['doctor_name']); ?>">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>Dr. <?php echo htmlspecialchars($order['doctor_name']); ?></strong>
                                                <span class="text-muted ms-2"><?php echo htmlspecialchars($order['specialization']); ?></span>
                                            </div>
                                            <div class="order-date">
                                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                            </div>
                                        </div>
                                        <div class="order-text mt-2">
                                            <?php echo nl2br(htmlspecialchars($order['order_text'])); ?>
                                        </div>
                                        <div class="text-end mt-2">
                                            <button class="btn btn-sm btn-primary">Request Appointment</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-requests">
                            <i class="bi bi-clipboard2-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h4 class="mt-3">No Available Referrals Found</h4>
                            <p class="text-muted">You don't have any medical orders or referrals available for scheduling.</p>
                            <p class="text-muted">Please contact your doctor if you need a referral for a diagnostic procedure.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="scheduled-procedures" role="tabpanel" aria-labelledby="scheduled-tab">
            <div class="card border-top-0 rounded-top-0">
                <div class="card-body">
                    <h5 class="card-title">Your Scheduled and Pending Procedures</h5>

                    <?php if ($scheduled_procedures->num_rows > 0): ?>
                        <?php while($procedure = $scheduled_procedures->fetch_assoc()): ?>
                            <div class="procedure-request">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="request-doctor">Dr. <?php echo htmlspecialchars($procedure['doctor_name']); ?></span>

                                        <?php if (strpos($procedure['order_text'], '[SCHEDULING PENDING]') !== false): ?>
                                            <span class="ms-2 status-pending">Pending Scheduling</span>
                                        <?php elseif (strpos($procedure['order_text'], '[SCHEDULED]') !== false): ?>
                                            <span class="ms-2 status-scheduled">Scheduled</span>
                                        <?php elseif (strpos($procedure['order_text'], '[COMPLETED]') !== false): ?>
                                            <span class="ms-2 status-completed">Completed</span>
                                        <?php elseif (strpos($procedure['order_text'], '[REJECTED]') !== false): ?>
                                            <span class="ms-2 status-rejected">Rejected</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="request-date">
                                        <?php echo date('F d, Y', strtotime($procedure['order_date'])); ?>
                                    </div>
                                </div>
                                <div class="request-text">
                                    <?php echo nl2br(htmlspecialchars($procedure['order_text'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-requests">
                            <i class="bi bi-clipboard2-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h4 class="mt-3">No Scheduled Procedures</h4>
                            <p class="text-muted">You don't have any scheduled or pending diagnostic procedures.</p>
                            <p class="text-muted">If you have a referral, you can request an appointment from the Available Referrals tab.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Procedure Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Request Appointment for Procedure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Doctor</label>
                        <input type="text" class="form-control" id="modal-doctor" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Medical Order</label>
                        <textarea class="form-control" id="modal-order-text" rows="4" readonly></textarea>
                    </div>

                    <input type="hidden" id="order-id" name="order_id" value="">

                    <div class="mb-3">
                        <label for="preferred_date" class="form-label">Preferred Date (Optional)</label>
                        <input type="date" class="form-control" id="preferred_date" name="preferred_date"
                               min="<?php echo date('Y-m-d'); ?>">
                        <div class="form-text">If you have a preferred date, please select it. Otherwise, the facility will assign the next available slot.</div>
                    </div>

                    <div class="mb-3">
                        <label for="preferred_time" class="form-label">Preferred Time (Optional)</label>
                        <select class="form-select" id="preferred_time" name="preferred_time">
                            <option value="">No preference</option>
                            <option value="Morning">Morning (8AM-12PM)</option>
                            <option value="Afternoon">Afternoon (12PM-4PM)</option>
                            <option value="Evening">Evening (4PM-8PM)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="additional_notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3"
                                  placeholder="Any additional information that might help with scheduling your procedure"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_request" class="btn btn-primary">Request Appointment</button>
                </div>
            </form>
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
    // Populate the schedule modal with order details
    document.addEventListener('DOMContentLoaded', function() {
        const scheduleModal = document.getElementById('scheduleModal');
        if (scheduleModal) {
            scheduleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                const orderText = button.getAttribute('data-order-text');
                const doctor = button.getAttribute('data-doctor');

                document.getElementById('order-id').value = orderId;
                document.getElementById('modal-order-text').textContent = orderText;
                document.getElementById('modal-doctor').value = "Dr. " + doctor;
            });
        }
    });
</script>
</body>
</html>-icon"></span>
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
            <a class="nav-link" href="medical_orders.php">Medical Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="diagnostic_procedures.php">Diagnostic Procedures</a>
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
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic Procedures</h2>
            <p class="text-muted">Request and track diagnostic procedures such as MRIs, CT scans, and laboratory tests</p>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newProcedureModal">
                <i class="bi bi-plus-circle me-1"></i> Request New Procedure
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Diagnostic Procedure Requests</h5>
                </div>
                <div class="card-body">
                    <?php if ($procedure_requests->num_rows > 0): ?>
                        <?php while($request = $procedure_requests->fetch_assoc()): ?>
                            <div class="procedure-request">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="request-doctor">Dr. <?php echo htmlspecialchars($request['doctor_name']); ?></span>

                                        <?php if (strpos($request['order_text'], '[THIS IS A PATIENT-INITIATED REQUEST PENDING DOCTOR APPROVAL]') !== false): ?>
                                            <span class="ms-2 status-pending">Pending Approval</span>
                                        <?php elseif (strpos($request['order_text'], '[APPROVED]') !== false): ?>
                                            <span class="ms-2 status-approved">Approved</span>
                                        <?php elseif (strpos($request['order_text'], '[REJECTED]') !== false): ?>
                                            <span class="ms-2 status-rejected">Rejected</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="request-date">
                                        <?php echo date('F d, Y', strtotime($request['order_date'])); ?>
                                    </div>
                                </div>
                                <div class="request-text">
                                    <?php
                                    // Clean up the order text for display (remove the status tag)
                                    $display_text = str_replace(
                                        ['[THIS IS A PATIENT-INITIATED REQUEST PENDING DOCTOR APPROVAL]', '[APPROVED]', '[REJECTED]'],
                                        '',
                                        $request['order_text']
                                    );
                                    echo nl2br(htmlspecialchars(trim($display_text)));
                                    ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-requests">
                            <i class="bi bi-clipboard2-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h4 class="mt-3">No Diagnostic Procedure Requests Found</h4>
                            <p class="text-muted">You haven't requested any diagnostic procedures yet.</p>
                            <p class="text-muted">Click the "Request New Procedure" button to get started.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Diagnostic Procedure Request Modal -->
<div class="modal fade" id="newProcedureModal" tabindex="-1" aria-labelledby="newProcedureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="newProcedureModalLabel">Request Diagnostic Procedure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="doctor_id" class="form-label">Select Doctor</label>
                        <select class="form-select" id="doctor_id" name="doctor_id" required>
                            <option value="">-- Select Doctor --</option>
                            <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                <option value="<?php echo $doctor['doctor_id']; ?>">
                                    Dr. <?php echo htmlspecialchars($doctor['username']); ?> (<?php echo htmlspecialchars($doctor['specialization']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="procedure_type" class="form-label">Procedure Type</label>
                        <select class="form-select" id="procedure_type" name="procedure_type" required>
                            <option value="">-- Select Type --</option>
                            <?php foreach ($procedure_types as $type => $procedures): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="specific_procedure" class="form-label">Specific Procedure</label>
                        <select class="form-select" id="specific_procedure" name="specific_procedure" required disabled>
                            <option value="">-- Select Procedure Type First --</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="preferred_date" class="form-label">Preferred Date</label>
                                <input type="date" class="form-control" id="preferred_date" name="preferred_date" required
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="preferred_time" class="form-label">Preferred Time</label>
                                <select class="form-select" id="preferred_time" name="preferred_time" required>
                                    <option value="">-- Select Time --</option>
                                    <option value="Morning (8AM-12PM)">Morning (8AM-12PM)</option>
                                    <option value="Afternoon (12PM-4PM)">Afternoon (12PM-4PM)</option>
                                    <option value="Evening (4PM-8PM)">Evening (4PM-8PM)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="additional_info" class="form-label">Additional Information</label>
                        <textarea class="form-control" id="additional_info" name="additional_info" rows="3"
                                  placeholder="Please provide any relevant medical history, symptoms, or other information that might be helpful"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_request" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
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
    // Populate the specific procedures dropdown based on the selected procedure type
    document.getElementById('procedure_type').addEventListener('change', function() {
        const procedureType = this.value;
        const specificProcedureSelect = document.getElementById('specific_procedure');

        // Clear the current options
        specificProcedureSelect.innerHTML = '';

        if (procedureType) {
            // Enable the specific procedure dropdown
            specificProcedureSelect.disabled = false;

            // Add the default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Select Specific Procedure --';
            specificProcedureSelect.appendChild(defaultOption);

            // Add the specific procedures for the selected type
            const procedureTypes = <?php echo json_encode($procedure_types); ?>;

            if (procedureTypes[procedureType]) {
                procedureTypes[procedureType].forEach(function(procedure) {
                    const option = document.createElement('option');
                    option.value = procedure;
                    option.textContent = procedure;
                    specificProcedureSelect.appendChild(option);
                });
            }
        } else {
            // Disable the specific procedure dropdown if no type is selected
            specificProcedureSelect.disabled = true;

            // Add the default option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '-- Select Procedure Type First --';
            specificProcedureSelect.appendChild(defaultOption);
        }
    });
</script>
</body>
</html>