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

// In a real application, you'd check if the user is logged in
// For development, we'll use a test patient ID
$patient_id = $_SESSION['user_id'] ?? 3002; // Default to 3002 for testing

// Variables to store form data and messages
$appointment_id = null;
$doctor_id = null;
$facility_id = null;
$current_date = null;
$error_message = null;
$success_message = null;
$available_slots = [];

// Handle the initial request with an appointment ID
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $appointment_id = intval($_GET['id']);

    // Get current appointment details
    $query = "SELECT a.*, u.username as doctor_name, d.specialization,
              f.name as facility_name, f.facility_id
              FROM appointments a 
              JOIN doctors d ON a.doctor_id = d.doctor_id 
              JOIN users u ON d.doctor_id = u.user_id
              JOIN facilities f ON a.facility_id = f.facility_id
              WHERE a.appointment_id = ? AND a.patient_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $appointment_id, $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $doctor_id = $appointment['doctor_id'];
        $facility_id = $appointment['facility_id'];
        $current_date = $appointment['appointment_date'];
    } else {
        $error_message = "Appointment not found or you don't have permission to reschedule it.";
    }
    $stmt->close();
}

// Handle form submission to update the appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reschedule'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_date = $_POST['new_date'] . ' ' . $_POST['new_time'];

    // Validate the selected date (not in the past)
    $current_datetime = date('Y-m-d H:i:s');
    if ($new_date <= $current_datetime) {
        $error_message = "Please select a future date and time.";
    } else {
        // Check if the slot is available (no other appointments at the same time for the doctor)
        $check_query = "SELECT appointment_id FROM appointments 
                        WHERE doctor_id = ? 
                        AND appointment_date = ? 
                        AND appointment_id != ?";

        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("isi", $doctor_id, $new_date, $appointment_id);
        $stmt->execute();
        $check_result = $stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "This time slot is already booked. Please select another time.";
        } else {
            // Update the appointment
            $update_query = "UPDATE appointments 
                             SET appointment_date = ? 
                             WHERE appointment_id = ? AND patient_id = ?";

            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sii", $new_date, $appointment_id, $patient_id);

            if ($stmt->execute()) {
                $success_message = "Appointment successfully rescheduled.";

                // Add a small delay before redirecting to show the success message
                header("refresh:1;url=appointments.php");
            } else {
                $error_message = "Error rescheduling appointment: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

// Get available time slots for the selected doctor and facility
// For a real application, you would check the doctor's schedule
// We'll use some generic time slots for demo purposes
$start_hour = 8; // 8 AM
$end_hour = 17;  // 5 PM
$interval = 30;  // 30-minute intervals

// Function to get next 14 days excluding weekends
function getNextWorkingDays($daysToShow = 14) {
    $workingDays = [];
    $day = 0;
    $count = 0;

    while ($count < $daysToShow) {
        $date = date('Y-m-d', strtotime("+$day days"));
        $dayOfWeek = date('N', strtotime($date));

        // Skip weekends (6=Saturday, 7=Sunday)
        if ($dayOfWeek < 6) {
            $workingDays[] = $date;
            $count++;
        }

        $day++;
    }

    return $workingDays;
}

// Get next 14 working days
$available_dates = getNextWorkingDays(14);

// Function to generate time slots
function generateTimeSlots($start, $end, $interval) {
    $slots = [];
    for ($hour = $start; $hour < $end; $hour++) {
        for ($minute = 0; $minute < 60; $minute += $interval) {
            if ($hour == $end - 1 && $minute > 30) {
                continue; // Skip slots past end time
            }
            $time = sprintf("%02d:%02d:00", $hour, $minute);
            $slots[] = $time;
        }
    }
    return $slots;
}

$available_times = generateTimeSlots($start_hour, $end_hour, $interval);

// Generate doctor's schedule (in a real app, this would come from the database)
// For now, we'll assume all listed times are available except for existing appointments
$booked_slots = [];
if ($doctor_id) {
    $booked_query = "SELECT appointment_date FROM appointments 
                    WHERE doctor_id = ? 
                    AND appointment_date > NOW() 
                    AND appointment_id != ?";

    $stmt = $conn->prepare($booked_query);
    $stmt->bind_param("ii", $doctor_id, $appointment_id);
    $stmt->execute();
    $booked_result = $stmt->get_result();

    while ($row = $booked_result->fetch_assoc()) {
        $booked_slots[] = $row['appointment_date'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Appointment | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .date-card {
            cursor: pointer;
            transition: all 0.2s;
        }
        .date-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .date-card.selected {
            background-color: #e9f5ff;
            border-color: #0d6efd;
        }
        .time-slot {
            cursor: pointer;
            transition: all 0.2s;
        }
        .time-slot:hover {
            background-color: #f8f9fa;
        }
        .time-slot.selected {
            background-color: #e9f5ff;
            border-color: #0d6efd;
        }
        .time-slot.booked {
            background-color: #f8d7da;
            color: #842029;
            cursor: not-allowed;
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
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-plus me-2"></i>Reschedule Appointment</h2>
        <a href="appointments.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Appointments
        </a>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($appointment_id && !isset($success_message)): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Current Appointment Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($appointment['specialization']); ?></p>
                        <p><strong>Facility:</strong> <?php echo htmlspecialchars($appointment['facility_name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Current Date:</strong> <?php echo date('l, F j, Y', strtotime($current_date)); ?></p>
                        <p><strong>Current Time:</strong> <?php echo date('h:i A', strtotime($current_date)); ?></p>
                        <?php if (!empty($appointment['reason'])): ?>
                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($appointment['reason']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Select New Date and Time</h5>
            </div>
            <div class="card-body">
                <form method="post" id="rescheduleForm">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">

                    <div class="mb-4">
                        <label class="form-label">Select Date</label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($available_dates as $index => $date): ?>
                                        <?php
                                        $dayName = date('D', strtotime($date));
                                        $dayNum = date('j', strtotime($date));
                                        $monthName = date('M', strtotime($date));
                                        ?>
                                        <div class="date-card card text-center p-2" data-date="<?php echo $date; ?>">
                                            <div class="small text-muted"><?php echo $dayName; ?></div>
                                            <div class="fw-bold"><?php echo $dayNum; ?></div>
                                            <div class="small text-muted"><?php echo $monthName; ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="new_date" id="selectedDate" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Select Time</label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-2" id="timeSlots">
                                    <?php foreach ($available_times as $time): ?>
                                        <div class="time-slot btn btn-outline-secondary" data-time="<?php echo $time; ?>">
                                            <?php echo date('h:i A', strtotime($time)); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="new_time" id="selectedTime" required>
                    </div>

                    <div class="text-end">
                        <a href="appointments.php" class="btn btn-outline-secondary me-2">Cancel</a>
                        <button type="submit" name="reschedule" class="btn btn-primary" id="submitButton" disabled>
                            <i class="bi bi-calendar-check me-2"></i>Confirm Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-light text-center text-muted py-3 mt-5">
    <div class="container">
        <p class="mb-0">&copy; 2025 Healthcare System. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store booked slots from PHP to JavaScript
        const bookedSlots = <?php echo json_encode($booked_slots); ?>;

        // Date selection logic
        const dateCards = document.querySelectorAll('.date-card');
        const timeSlots = document.querySelectorAll('.time-slot');
        const selectedDateInput = document.getElementById('selectedDate');
        const selectedTimeInput = document.getElementById('selectedTime');
        const submitButton = document.getElementById('submitButton');

        let selectedDate = null;
        let selectedTime = null;

        // Function to check if a date-time is booked
        function isTimeSlotBooked(date, time) {
            const dateTimeStr = `${date} ${time}`;
            return bookedSlots.includes(dateTimeStr);
        }

        // Function to update time slots based on selected date
        function updateTimeSlots() {
            if (!selectedDate) return;

            timeSlots.forEach(slot => {
                const time = slot.getAttribute('data-time');
                if (isTimeSlotBooked(selectedDate, time)) {
                    slot.classList.add('booked');
                    slot.setAttribute('title', 'This time slot is already booked');
                } else {
                    slot.classList.remove('booked');
                    slot.removeAttribute('title');
                }
            });
        }

        // Function to check if form is valid
        function checkFormValidity() {
            if (selectedDate && selectedTime) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        // Date card click handler
        dateCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                dateCards.forEach(c => c.classList.remove('selected'));

                // Add selected class to clicked card
                this.classList.add('selected');

                // Update selected date
                selectedDate = this.getAttribute('data-date');
                selectedDateInput.value = selectedDate;

                // Reset time selection
                timeSlots.forEach(slot => slot.classList.remove('selected'));
                selectedTime = null;
                selectedTimeInput.value = '';

                // Update available time slots
                updateTimeSlots();

                // Check form validity
                checkFormValidity();
            });
        });

        // Time slot click handler
        timeSlots.forEach(slot => {
            slot.addEventListener('click', function() {
                // Skip if slot is booked
                if (this.classList.contains('booked')) return;

                // Remove selected class from all slots
                timeSlots.forEach(s => s.classList.remove('selected'));

                // Add selected class to clicked slot
                this.classList.add('selected');

                // Update selected time
                selectedTime = this.getAttribute('data-time');
                selectedTimeInput.value = selectedTime;

                // Check form validity
                checkFormValidity();
            });
        });
    });
</script>
</body>
</html>