<?php
header("Content-Type: application/json");
$conn = require_once '../../db.php'; // adjust path to your db.php

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Utility: parse JSON input for PUT and DELETE
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

switch ($requestMethod) {

    // CREATE
    case 'POST':
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $role = $_POST['role'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $jmbg = $_POST['jmbg'];
        $dob = $_POST['dob'];

        $stmt = $conn->prepare("INSERT INTO users (password, email, role, first_name, last_name, jmbg, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $password, $email, $role, $first_name, $last_name, $jmbg, $dob);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Role-specific entry
            if ($role == "doctor") {
                $specialization = $_POST['specialization'];
                $stmt2 = $conn->prepare("INSERT INTO doctors (specialization, user_id) VALUES (?, ?)");
                $stmt2->bind_param("si", $specialization, $user_id);
                $stmt2->execute();
            } elseif ($role == "patient") {
                $date_of_birth = $_POST['dob'];
                $gender = $_POST['gender'];
                $stmt3 = $conn->prepare("INSERT INTO patients (date_of_birth, gender, user_id) VALUES (?, ?, ?)");
                $stmt3->bind_param("ssi", $date_of_birth, $gender, $user_id);
                $stmt3->execute();
            }

            echo json_encode(["success" => true, "user_id" => $user_id]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;

//        READ
    case 'GET':
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];

            $stmt = $conn->prepare("
            SELECT 
                u.*, 
                d.specialization, 
                p.gender, 
                p.date_of_birth AS patient_dob
            FROM users u
            LEFT JOIN doctors d ON u.user_id = d.user_id
            LEFT JOIN patients p ON u.user_id = p.user_id
            WHERE u.user_id = ?
        ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
        } else {
            // Fetch all users with role info
            $result = $conn->query("
            SELECT 
                u.*, 
                d.specialization, 
                p.gender, 
                p.date_of_birth AS patient_dob
            FROM users u
            LEFT JOIN doctors d ON u.user_id = d.user_id
            LEFT JOIN patients p ON u.user_id = p.user_id
        ");

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            echo json_encode($users);
        }
        break;

    // UPDATE
    case 'PUT':
        $user_id = $_POST['user_id'];
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $jmbg = $_POST['jmbg'];
        $dob = $_POST['dob'];
        $role = $_POST['role']; // Make sure to include this in your form/post

        // Update basic user info
        $stmt = $conn->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, jmbg = ?, dob = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $email, $first_name, $last_name, $jmbg, $dob, $user_id);

        if ($stmt->execute()) {
            // Update role-specific field
            if ($role === 'doctor' && isset($_POST['specialization'])) {
                $specialization = $_POST['specialization'];
                $stmt2 = $conn->prepare("UPDATE doctors SET specialization = ? WHERE user_id = ?");
                $stmt2->bind_param("si", $specialization, $user_id);
                $stmt2->execute();
            }

            if ($role === 'patient' && isset($_POST['gender'])) {
                $gender = $_POST['gender'];
                $stmt3 = $conn->prepare("UPDATE patients SET gender = ? WHERE user_id = ?");
                $stmt3->bind_param("si", $gender, $user_id);
                $stmt3->execute();
            }

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
    break;

    // DELETE - Remove appointment
    case 'DELETE':
        $user_id = $_POST['user_id'];

        // Get role first
        $stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $role_result = $stmt->get_result()->fetch_assoc();
        $role = $role_result['role'];

        // Delete from related table first
        if ($role === 'doctor') {
            $conn->query("DELETE FROM doctors WHERE user_id = $user_id");
        } elseif ($role === 'patient') {
            $conn->query("DELETE FROM patients WHERE user_id = $user_id");
        }

        // Now delete the user
        $stmt2 = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt2->bind_param("i", $user_id);
        if ($stmt2->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt2->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Method not allowed"]);
        break;
}
?>
