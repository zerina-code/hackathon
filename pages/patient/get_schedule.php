<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_GET['doctor_id'] ?? 0;

$events = [];

// Get next 14 days
$today = date('Y-m-d');
$end = date('Y-m-d', strtotime('+14 days'));

// Svi slobodni slotovi (pretpostavljeni raspored)
$sql = "SELECT date, time FROM doctor_schedule 
        WHERE doctor_id = ? AND date BETWEEN ? AND ? 
        AND (date, time) NOT IN (
            SELECT DATE(appointment_date), TIME(appointment_date) 
            FROM appointments WHERE doctor_id = ?
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $doctor_id, $today, $end, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $datetime = $row['date'] . 'T' . $row['time'];
    $events[] = [
        'title' => 'Available',
        'start' => $datetime,
        'end' => date('Y-m-d\TH:i:s', strtotime($datetime . ' +30 minutes')),
        'color' => '#28a745'
    ];
}

echo json_encode($events);
?>
