<?php
header("Content-Type: application/json");
$conn = require_once '../../db.php'; // adjust path to your db.php

$requestMethod = $_SERVER["REQUEST_METHOD"];

// Utility: parse JSON input for PUT and DELETE
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

switch ($requestMethod) {

    // CREATE - Add a new appointment
    case 'POST':
        $patient_id = $_POST['patient_id'];
        $doctor_id = $_POST['doctor_id'];
        $appointment_date = $_POST['appointment_date'];
        $reason = $_POST['reason'];

        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $patient_id, $doctor_id, $appointment_date, $reason);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "appointment_id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;

    // READ - Get appointments or a specific one
    case 'GET':
        if (isset($_GET['appointment_id'])) {
            $appointment_id = $_GET['appointment_id'];
            $stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = ?");
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                echo json_encode(["error" => "Appointment not found"]);
            }
        } else {
            $stmt = $conn->prepare("SELECT * FROM appointments");
            $stmt->execute();
            $result = $stmt->get_result();
            $appointments = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($appointments);
        }
        break;

    // UPDATE - Update appointment
    case 'PUT':
        $data = getJsonInput();
        $appointment_id = isset($_GET['appointment_id']) ? $_GET['appointment_id'] : null;

        if (!$appointment_id) {
            echo json_encode(["success" => false, "error" => "Missing appointment_id"]);
            exit;
        }

        $patient_id = $data['patient_id'];
        $doctor_id = $data['doctor_id'];
        $appointment_date = $data['appointment_date'];
        $reason = $data['reason'];

        $stmt = $conn->prepare("UPDATE appointments SET patient_id = ?, doctor_id = ?, appointment_date = ?, reason = ? WHERE appointment_id = ?");
        $stmt->bind_param("iissi", $patient_id, $doctor_id, $appointment_date, $reason, $appointment_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;

    // DELETE - Remove appointment
    case 'DELETE':
        $appointment_id = isset($_GET['appointment_id']) ? $_GET['appointment_id'] : null;

        if (!$appointment_id) {
            echo json_encode(["success" => false, "error" => "Missing appointment_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "error" => "Method not allowed"]);
        break;
}
?>
