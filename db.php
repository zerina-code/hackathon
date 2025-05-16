<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "Healthcare";

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Konekcija nije uspjela: " . $conn->connect_error);
}

// Ako je konekcija uspješna
// echo "Uspješno povezano s bazom podataka!";
return $conn;
?>
