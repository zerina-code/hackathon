<?php

function sendPostRequest($data) {
    $url = 'http://localhost:8001/php/users/create-user.php'; // Change this to the actual path

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch) . "\n";
    } else {
        echo "Response: $response\n";
    }

    curl_close($ch);
}

// Test case 1: Doctor registration
$doctorData = [
    'password' => 'DocPass123',
    'email' => 'doc@example.com',
    'role' => 'doctor',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'jmbg' => '1234567890123',
    'dob' => '1980-01-01',
    'specialization' => 'Cardiology'
];
echo "Testing doctor registration:\n";
sendPostRequest($doctorData);

// Test case 2: Patient registration
$patientData = [
    'password' => 'PatPass456',
    'email' => 'patient@example.com',
    'role' => 'patient',
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'jmbg' => '9876543210987',
    'dob' => '1995-05-15',
    'gender' => 'female'
];
echo "Testing patient registration:\n";
sendPostRequest($patientData);

// Test case 3: Invalid request (missing fields)
$invalidData = [
    'email' => 'missing@example.com',
    'role' => 'doctor'
];
echo "Testing invalid request:\n";
sendPostRequest($invalidData);

?>
