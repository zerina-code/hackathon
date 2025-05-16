<?php
include_once '../../db.php';

// SQL Query to fetch all appointments
$sql = "
    SELECT 
        CONCAT('Dr. ', du.first_name, ' ', du.last_name) AS doctorName,
        d.specialization AS speciality,
        CONCAT(pu.first_name, ' ', pu.last_name) AS patientName,
        a.appointment_date AS time,
        CASE 
            WHEN a.status = 1 THEN 'Completed'
            WHEN a.status = 0 THEN 'Pending'
            ELSE 'Unknown'
        END AS status,
        a.amount AS amount
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.user_id
    JOIN users du ON d.user_id = du.user_id
    JOIN users pu ON a.patient_id = pu.user_id
    ORDER BY a.appointment_date DESC
";

// Execute the query
$result = $conn->query($sql);

// Initialize an empty array to store appointments
$appointments = [];

// Fetch all the data
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'doctorName' => $row['doctorName'],
        'speciality' => $row['speciality'],
        'patientName' => $row['patientName'],
        'time' => date('Y-m-d H:i A', strtotime($row['time'])),
        'status' => $row['status'],
        'amount' => $row['amount']
    ];
}

// Return the data as JSON response
header('Content-Type: application/json');
echo json_encode($appointments);
?>
