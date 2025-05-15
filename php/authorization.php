<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'healthcare');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Prevent displaying this page directly - redirect to registration page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Location: ../pages/registration-page.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../pages/registration-page.php');
    exit;
}

// Get form values
$action = isset($_POST['form_action']) ? $_POST['form_action'] : 'signin';
$email = filter_var(trim(isset($_POST['email']) ? $_POST['email'] : ''), FILTER_SANITIZE_EMAIL);
$password = trim(isset($_POST['psw']) ? $_POST['psw'] : '');
$role = 'patient'; // Default role for signup
$firstName = trim(isset($_POST['fname']) ? $_POST['fname'] : '');
$lastName = trim(isset($_POST['lname']) ? $_POST['lname'] : '');
$dob = isset($_POST['dob']) ? $_POST['dob'] : '';
$jmbg = trim(isset($_POST['jmbg']) ? $_POST['jmbg'] : '');

// Validate email and password
if (empty($email) || empty($password)) {
    header("Location: ../pages/registration-page.php?mode=$action&error=missing_fields&email=" . urlencode($email));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/registration-page.php?mode=$action&error=invalid_email&email=" . urlencode($email));
    exit;
}

// Validation logic for signup (same as before)
if ($action === 'signup') {
    // Your existing signup validation code...

    // Hash password and insert user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (password, email, role, first_name, last_name, jmbg) 
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $hashedPassword, $email, $role, $firstName, $lastName, $jmbg);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../pages/dashboard.php?registered=true");
        exit;
    } else {
        $error = "Database error: " . mysqli_error($conn);
        header("Location: ../pages/registration-page.php?mode=signup&error=" . urlencode($error));
        exit;
    }

} elseif ($action === 'signin') {
    // Email and password already validated above

    // Prepare the query to select the user based on the provided email
    $stmt = mysqli_prepare($conn, "SELECT user_id, password, role FROM users WHERE email = ?");

    if (!$stmt) {
        die('Failed to prepare the query: ' . mysqli_error($conn));
    }

    // Bind the email parameter
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Execute the query
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    // Check if a row was returned
    if (mysqli_stmt_num_rows($stmt) === 1) {
        // Bind the results from the query
        mysqli_stmt_bind_result($stmt, $userId, $hashedPasswordFromDB, $userRole);

        // Fetch the result
        mysqli_stmt_fetch($stmt);

        // Verify password
        if (password_verify($password, $hashedPasswordFromDB)) {
            // Start session to store user info
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['role'] = $userRole;

            // Redirect based on role
            if ($userRole === 'admin') {
                header("Location: ../pages/admin/dashboard.php");
            } else {
                header("Location: ../pages/dashboard.php");
            }
            exit;
        } else {
            // Password doesn't match
            header("Location: ../pages/registration-page.php?mode=signin&error=invalid_credentials&email=" . urlencode($email));
            exit;
        }
    } else {
        // No user found with that email
        header("Location: ../pages/registration-page.php?mode=signin&error=user_not_found&email=" . urlencode($email));
        exit;
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// If no match was found or password was incorrect
$error = "Invalid email or password.";
header("Location: ../pages/registration-page.php?mode=signin&error=" . urlencode($error));
exit;
?>