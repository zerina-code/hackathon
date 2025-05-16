<?php
$conn = include_once '../../db.php';

// SQL query to get patients data, including all required fields
$sql = "
    SELECT 
        CONCAT(first_name, ' ', last_name) AS name,
        date_of_birth AS dob,
        gender,
        user_id,
        is_insured,
        phone,
        last_visit AS lastVisit
    FROM patients
    ORDER BY last_visit DESC
";


// Execute the query
$result = $conn->query($sql);

// Initialize an empty array to store the patients data
$patients = [];

// Fetch all the data
while ($row = $result->fetch_assoc()) {
    $patients[] = [
        'patient_id' => $row['patient_id'], // Keep the patient ID as is
        'dob' => date('Y-m-d', strtotime($row['dob'])), // Format date of birth (dob)
        'gender' => $row['gender'], // Gender field
        'user_id' => $row['user_id'], // User ID
        'is_insured' => $row['is_insured'], // Insurance status
        'phone' => $row['phone'], // Patient's phone number
        'lastVisit' => date('Y-m-d', strtotime($row['lastVisit'])), // Last visit date, formatted
    ];
}

// Return the data as JSON response
header('Content-Type: application/json');
echo json_encode($patients);
?>
