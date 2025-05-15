<?php
$conn = mysqli_connect('localhost', 'root', '', 'Healthcare');

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../pages/Users/registration-page.php');
    exit;
}

$action = isset($_POST['form_action']) ? $_POST['form_action'] : 'signin';
$email = filter_var(trim(isset($_POST['email']) ? $_POST['email'] : ''), FILTER_SANITIZE_EMAIL);
$password = trim(isset($_POST['psw']) ? $_POST['psw'] : '');
$username = trim(isset($_POST['username']) ? $_POST['username'] : '');

if (empty($email) || empty($password) || ($action === 'signup' && empty($username))) {
    header("Location: ../pages/Users/registration-page.php?mode=$action&error=missing_fields&username=" . urlencode($username) . "&email=" . urlencode($email));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/Users/registration-page.php?mode=$action&error=invalid_email&username=" . urlencode($username));
    exit;
}

if ($action === 'signup') {
    $stmt = mysqli_prepare($conn, "SELECT UID FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        header("Location: ../pages/registration-page.php?mode=signup&error=email_exists&username=" . urlencode($username));
        exit;
    }
    mysqli_stmt_close($stmt);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO User (email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../views/dashboard.php?registered=true");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);

} elseif ($action === 'signin') {
    $stmt = mysqli_prepare($conn, "SELECT user_id, username, password FROM User WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $userId, $username, $hashedPasswordFromDB);
        mysqli_stmt_fetch($stmt);

        if (password_verify($password, $hashedPasswordFromDB)) {
            header("Location: ../pages/Admin/dashboard.php");
            exit;
        }
    }

    header("Location: ../pages/Users/registration-page.php?mode=signin&error=invalid_credentials&email=" . urlencode($email));
    exit;
}

mysqli_close($conn);
?>
