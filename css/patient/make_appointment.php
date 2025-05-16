<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// In a real application, you'd check if the user is logged in
// For development, we'll use a test patient ID
$patient_id = $_SESSION['user_id'] ?? 3; // Default to 3 for testing

// Get patient information
$query = "SELECT p.patient_id, p.is_insured, u.username, u.email, p.date_of_birth, p.gender 
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
        'gender' => 'Not specified',
        'is_insured' => 0
    ];
    $error_message = "Patient data not found. Please ensure you're logged in correctly.";
}

// Get available doctors
$doctors_query = "SELECT d.doctor_id, u.username as doctor_name, d.specialization 
                 FROM doctors d 
                 JOIN users u ON d.doctor_id = u.user_id 
                 ORDER BY d.specialization, u.username";
$doctors_result = $conn->query($doctors_query);

// Get available facilities
$facilities_query = "SELECT facility_id, name, address FROM facilities ORDER BY name";
$facilities_result = $conn->query($facilities_query);

// Get available time slots (next 14 days)
$time_slots = [];
$start_time = 8; // 8 AM
$end_time = 17; // 5 PM
$interval = 30; // 30 minute intervals

for ($day = 0; $day < 14; $day++) {
    $date = date('Y-m-d', strtotime("+$day days"));

    // Skip weekends
    $dayOfWeek = date('N', strtotime($date));
    if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
        continue;
    }

    for ($hour = $start_time; $hour < $end_time; $hour++) {
        for ($minute = 0; $minute < 60; $minute += $interval) {
            $time = sprintf("%02d:%02d:00", $hour, $minute);
            $time_slots[] = [
                'date' => $date,
                'time' => $time,
                'datetime' => "$date $time"
            ];
        }
    }
}

// Get booked appointment slots to exclude them
$booked_slots_query = "SELECT appointment_date FROM appointments WHERE appointment_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 14 DAY)";
$booked_slots_result = $conn->query($booked_slots_query);
$booked_slots = [];

if ($booked_slots_result && $booked_slots_result->num_rows > 0) {
    while ($row = $booked_slots_result->fetch_assoc()) {
        $booked_slots[] = $row['appointment_date'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Appointment | Health Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
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
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($patient['username'] ?? 'Guest'); ?>
                    </span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>Schedule Your Appointment
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!isset($patient['is_insured']) || $patient['is_insured'] == 0): ?>
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <strong>Please note:</strong> You are not currently insured. You will be responsible for covering the full cost of your appointment.
                            </div>
                        </div>
                    <?php endif; ?>

                    <form id="appointmentForm" method="POST" action="appointment_submit.php" class="needs-validation" novalidate>
                        <!-- Step 1: Doctor Selection -->
                        <div class="step" id="step1">
                            <h5 class="text-primary mb-4">Step 1: Choose a Doctor</h5>

                            <div class="mb-4">
                                <label for="specialization" class="form-label">Filter by Specialization</label>
                                <select id="specialization" class="form-select mb-3">
                                    <option value="">All Specializations</option>
                                    <?php
                                    $specs = [];
                                    if ($doctors_result && $doctors_result->num_rows > 0) {
                                        $doctors_result->data_seek(0);
                                        while ($doctor = $doctors_result->fetch_assoc()) {
                                            if (!in_array($doctor['specialization'], $specs)) {
                                                $specs[] = $doctor['specialization'];
                                                echo "<option value='" . htmlspecialchars($doctor['specialization']) . "'>" .
                                                    htmlspecialchars($doctor['specialization']) . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row" id="doctorCards">
                                <?php
                                if ($doctors_result && $doctors_result->num_rows > 0) {
                                    $doctors_result->data_seek(0);
                                    while ($doctor = $doctors_result->fetch_assoc()): ?>
                                        <div class="col-md-6 mb-3 doctor-card" data-specialization="<?php echo htmlspecialchars($doctor['specialization']); ?>">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input doctor-select" type="radio" name="doctor_id"
                                                               id="doctor<?php echo $doctor['doctor_id']; ?>"
                                                               value="<?php echo $doctor['doctor_id']; ?>" required>
                                                        <label class="form-check-label w-100" for="doctor<?php echo $doctor['doctor_id']; ?>">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="doctor-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                     style="width: 40px; height: 40px;">
                                                                    <i class="bi bi-person"></i>
                                                                </div>
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($doctor['doctor_name']); ?></h6>
                                                            </div>
                                                            <p class="text-muted mb-1 small">
                                                                <i class="bi bi-clipboard-plus me-1"></i>
                                                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                                                            </p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                } else {
                                    echo '<div class="col-12"><div class="alert alert-info">No doctors found in the database.</div></div>';
                                }
                                ?>
                            </div>

                            <div class="mt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary next-step" data-step="1" disabled>
                                    Continue to Facility Selection <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Facility Selection -->
                        <div class="step" id="step2" style="display: none;">
                            <h5 class="text-primary mb-4">Step 2: Choose a Facility</h5>

                            <div class="row" id="facilityCards">
                                <?php
                                if ($facilities_result && $facilities_result->num_rows > 0) {
                                    while ($facility = $facilities_result->fetch_assoc()): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input facility-select" type="radio" name="facility_id"
                                                               id="facility<?php echo $facility['facility_id']; ?>"
                                                               value="<?php echo $facility['facility_id']; ?>" required>
                                                        <label class="form-check-label w-100" for="facility<?php echo $facility['facility_id']; ?>">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <div class="facility-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                     style="width: 40px; height: 40px;">
                                                                    <i class="bi bi-building"></i>
                                                                </div>
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($facility['name']); ?></h6>
                                                            </div>
                                                            <p class="text-muted mb-0 small">
                                                                <i class="bi bi-geo-alt me-1"></i>
                                                                <?php echo htmlspecialchars($facility['address']); ?>
                                                            </p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                } else {
                                    echo '<div class="col-12"><div class="alert alert-info">No facilities found in the database.</div></div>';
                                }
                                ?>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Doctor Selection
                                </button>
                                <button type="button" class="btn btn-primary next-step" data-step="2" disabled>
                                    Continue to Date & Time <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Date and Time Selection -->
                        <div class="step" id="step3" style="display: none;">
                            <h5 class="text-primary mb-4">Step 3: Choose Date & Time</h5>

                            <div id="calendarContainer" class="mb-4">
                                <div id="calendar"></div>
                            </div>

                            <div id="timeSlots" class="d-none">
                                <h6>Available Time Slots</h6>
                                <div class="row time-slots-container">
                                    <!-- Time slots will be populated dynamically -->
                                </div>
                            </div>

                            <input type="hidden" name="appointment_date" id="selectedDateTime" required>

                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Facility Selection
                                </button>
                                <button type="button" class="btn btn-primary next-step" data-step="3" disabled>
                                    Continue to Details <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Additional Details -->
                        <div class="step" id="step4" style="display: none;">
                            <h5 class="text-primary mb-4">Step 4: Appointment Details</h5>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason for Visit</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide a reason for your visit.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="symptoms" class="form-label">Symptoms (Optional)</label>
                                <textarea class="form-control" id="symptoms" name="symptoms" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="reminder" name="set_reminder" checked>
                                <label class="form-check-label" for="reminder">
                                    Send me a reminder 24 hours before appointment
                                </label>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Date Selection
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Confirm Appointment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Summary Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>Appointment Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div id="summaryEmpty" class="text-center py-4">
                        <i class="bi bi-calendar-plus" style="font-size: 2.5rem; color: var(--color-primary-accent);"></i>
                        <p class="mt-3 mb-0 text-muted">Complete the form to see your appointment summary</p>
                    </div>

                    <div id="summaryContent" style="display: none;">
                        <div class="mb-3">
                            <label class="text-muted">Doctor</label>
                            <p id="summaryDoctor" class="mb-0 fw-bold">-</p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted">Facility</label>
                            <p id="summaryFacility" class="mb-0 fw-bold">-</p>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted">Date & Time</label>
                            <p id="summaryDateTime" class="mb-0 fw-bold">-</p>
                        </div>

                        <div>
                            <label class="text-muted">Reason</label>
                            <p id="summaryReason" class="mb-0 fw-bold">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow-sm">
                <div class="card-body bg-light">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Need Help?
                    </h5>
                    <p class="text-muted">If you need assistance with booking your appointment, please contact us:</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2"></i>
                            <a href="tel:+12345678901">+1 (234) 567-8901</a>
                        </li>
                        <li>
                            <i class="bi bi-envelope me-2"></i>
                            <a href="mailto:support@healthcare.com">support@healthcare.com</a>
                        </li>
                    </ul>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    // Form step navigation
    document.addEventListener('DOMContentLoaded', function() {
        // Variables for summary
        let selectedDoctor = null;
        let selectedFacility = null;
        let selectedDate = null;

        // Doctor selection
        const doctorRadios = document.querySelectorAll('.doctor-select');
        const nextStep1Button = document.querySelector('.next-step[data-step="1"]');

        doctorRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                nextStep1Button.disabled = false;

                // Update summary
                const doctorCard = this.closest('.doctor-card');
                const doctorName = doctorCard.querySelector('h6').innerText;
                const specialization = doctorCard.querySelector('p').innerText;
                document.getElementById('summaryDoctor').innerText = doctorName;
                selectedDoctor = {
                    id: this.value,
                    name: doctorName,
                    specialization: specialization
                };

                document.getElementById('summaryEmpty').style.display = 'none';
                document.getElementById('summaryContent').style.display = 'block';
            });
        });

        // Facility selection
        const facilityRadios = document.querySelectorAll('.facility-select');
        const nextStep2Button = document.querySelector('.next-step[data-step="2"]');

        facilityRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                nextStep2Button.disabled = false;

                // Update summary
                const facilityCard = this.closest('.card');
                const facilityName = facilityCard.querySelector('h6').innerText;
                const facilityAddress = facilityCard.querySelector('p').innerText;
                document.getElementById('summaryFacility').innerText = facilityName;
                selectedFacility = {
                    id: this.value,
                    name: facilityName,
                    address: facilityAddress
                };
            });
        });

        // Specialization filter
        document.getElementById('specialization').addEventListener('change', function() {
            const selectedSpec = this.value;
            const doctorCards = document.querySelectorAll('.doctor-card');

            doctorCards.forEach(card => {
                if (!selectedSpec || card.dataset.specialization === selectedSpec) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Step navigation
        const steps = document.querySelectorAll('.step');

        document.querySelectorAll('.next-step').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = parseInt(this.dataset.step);
                steps[currentStep - 1].style.display = 'none';
                steps[currentStep].style.display = 'block';

                // Initialize calendar when going to step 3
                if (currentStep === 2) {
                    initializeCalendar();
                }
            });
        });

        document.querySelectorAll('.prev-step').forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = Array.from(steps).findIndex(step => step.style.display !== 'none');
                steps[currentStep].style.display = 'none';
                steps[currentStep - 1].style.display = 'block';
            });
        });

        // Calendar initialization
        function initializeCalendar() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                selectable: true,
                select: function(info) {
                    // Check if the selected date is a weekend
                    const dayOfWeek = info.start.getDay();
                    if (dayOfWeek === 0 || dayOfWeek === 6) {
                        alert('Weekends are not available for appointments. Please select a weekday.');
                        return;
                    }

                    // Format the date for display and API use
                    const formattedDate = info.startStr.split('T')[0];

                    // Show time slots for the selected date
                    showTimeSlots(formattedDate);
                }
            });

            calendar.render();
        }

        // Show time slots for selected date
        function showTimeSlots(date) {
            const timeSlotContainer = document.querySelector('.time-slots-container');
            timeSlotContainer.innerHTML = '';

            // Time slots from 8:00 AM to 5:00 PM in 30-minute intervals
            const startHour = 8;
            const endHour = 17;
            const interval = 30; // minutes

            // Get current date/time for disabling past slots
            const currentDate = new Date();
            const selectedDate = new Date(date);
            const isToday = selectedDate.toDateString() === currentDate.toDateString();

            // Temporarily showing all time slots
            // In a real app, you'd check these against booked appointments
            for (let hour = startHour; hour < endHour; hour++) {
                for (let minute = 0; minute < 60; minute += interval) {
                    const timeSlot = document.createElement('div');
                    timeSlot.className = 'col-md-4 col-6 mb-2';

                    const formattedHour = hour % 12 || 12;
                    const ampm = hour < 12 ? 'AM' : 'PM';
                    const formattedMinute = minute.toString().padStart(2, '0');
                    const displayTime = `${formattedHour}:${formattedMinute} ${ampm}`;

                    // Format for database (24-hour format)
                    const dbTime = `${hour.toString().padStart(2, '0')}:${formattedMinute}:00`;
                    const dbDateTime = `${date} ${dbTime}`;

                    // Check if time slot is in the past
                    let disabled = false;
                    if (isToday) {
                        const slotTime = new Date(selectedDate);
                        slotTime.setHours(hour, minute, 0);
                        disabled = slotTime < currentDate;
                    }

                    // For demo purposes: randomly disable some slots to simulate booked slots
                    // In a real app, you'd check against your database
                    const randomBooked = Math.random() > 0.7;

                    if (disabled || randomBooked) {
                        timeSlot.innerHTML = `
                                <button type="button" class="btn btn-outline-secondary w-100" disabled>
                                    ${displayTime}
                                </button>
                            `;
                    } else {
                        timeSlot.innerHTML = `
                                <button type="button" class="btn btn-outline-primary w-100 time-slot-btn" data-datetime="${dbDateTime}">
                                    ${displayTime}
                                </button>
                            `;
                    }

                    timeSlotContainer.appendChild(timeSlot);
                }
            }

            // Show time slots section
            document.getElementById('timeSlots').classList.remove('d-none');

            // Add click handlers for time slot buttons
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove selected class from all buttons
                    document.querySelectorAll('.time-slot-btn').forEach(b => {
                        b.classList.remove('active');
                    });

                    // Add selected class to clicked button
                    this.classList.add('active');

                    // Update hidden input
                    document.getElementById('selectedDateTime').value = this.dataset.datetime;

                    // Enable next button
                    document.querySelector('.next-step[data-step="3"]').disabled = false;

                    // Update summary
                    const dateObj = new Date(this.dataset.datetime);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
                    document.getElementById('summaryDateTime').innerText = dateObj.toLocaleDateString('en-US', options);
                    selectedDate = this.dataset.datetime;
                });
            });
        }

        // Update reason in summary
        document.getElementById('reason').addEventListener('input', function() {
            document.getElementById('summaryReason').innerText = this.value || '-';
        });

        // Form validation
        const form = document.getElementById('appointmentForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
</script>
</body>
</html>