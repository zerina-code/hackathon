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

//$patient_id = $_SESSION['user_id'] ?? null;
//if (!$patient_id) {
//    die("User not logged in.");
//}

$doctor_id = $_POST['doctor_id'] ?? null;
$appointment_date = $_POST['appointment_date'] ?? null;
$reason = $_POST['reason'] ?? '';

if (!$doctor_id || !$appointment_date) {
    die("All fields are required.");
}

// Prevent duplicate appointments
$checkSql = "SELECT * FROM appointments WHERE doctor_id = ? AND appointment_date = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("is", $doctor_id, $appointment_date);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    die("This time slot is already booked. Please choose another.");
}

// Save appointment
$insertSql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason) VALUES (?, ?, ?, ?)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $reason);

if ($insertStmt->execute()) {
    header("Location: success_page.php"); // napraviti jednostavnu stranicu sa porukom o uspješnom zakazivanju
    exit();
} else {
    echo "Error: " . $insertStmt->error;
}
?>
