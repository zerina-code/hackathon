<?php
session_start();
//require_once('../db_connection.php');
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Konekcija nije uspjela: " . $conn->connect_error);
}
// Get facility ID from request
$facility_id = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;

// Validate input
if ($facility_id <= 0) {
    echo json_encode([]);
    exit;
}

// Query to get doctors working at this facility
$query = "SELECT d.doctor_id, u.username as name, d.specialization, u.email
          FROM doctors d
          JOIN doctor_facilities df ON d.doctor_id = df.doctor_id
          JOIN users u ON d.user_id = u.user_id
          WHERE df.facility_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $facility_id);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($doctors);
?>