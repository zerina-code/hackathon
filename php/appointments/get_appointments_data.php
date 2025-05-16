<?php
// Include the database connection file
$conn=include_once '../../db.php';

// Start the session to get the logged-in doctor's ID
session_start();
if (!isset($_SESSION['doctor_id'])) {
    die("Doctor not logged in.");
}
$doctor_id = $_SESSION['doctor_id']; // Doctor's ID from session

// Initialize an empty array to store the data
$data = [];

// Get the distinct facilities the doctor works in
$sql_facilities = "SELECT DISTINCT facility_id FROM appointments WHERE doctor_id = ?";
$stmt_facilities = $conn->prepare($sql_facilities);
$stmt_facilities->bind_param("i", $doctor_id);
$stmt_facilities->execute();
$facility_result = $stmt_facilities->get_result();

// Store the facilities the doctor works in
$facilities = [];
while ($row = $facility_result->fetch_assoc()) {
    $facilities[] = $row['facility_id'];
}

// Now, for each facility, get the number of appointments per year
foreach ($facilities as $facility_id) {
    $sql_appointments = "SELECT YEAR(appointment_date) as year, COUNT(*) as total_appointments
                         FROM appointments 
                         WHERE doctor_id = ? AND facility_id = ? 
                         GROUP BY YEAR(appointment_date)
                         ORDER BY YEAR(appointment_date)";

    $stmt_appointments = $conn->prepare($sql_appointments);
    $stmt_appointments->bind_param("ii", $doctor_id, $facility_id);
    $stmt_appointments->execute();
    $appointments_result = $stmt_appointments->get_result();

    // Fetch data and store it in the $data array
    while ($row = $appointments_result->fetch_assoc()) {
        $data[] = [
            'year' => $row['year'],
            'facility_id' => $facility_id,
            'total_appointments' => $row['total_appointments']
        ];
    }
}

// Close the database connection
$conn->close();

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
