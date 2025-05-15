<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/registration.css">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Registracija</title>
</head>
<body class="signin-mode">
<section id="registration-section">
    <?php
    $formAction = "../php/authorization.php";
    $initialMode = isset($_GET['mode']) ? $_GET['mode'] : 'signin';
    ?>
    <div class="form-container">
        <div class="imgcontainer">
<!--            <img src="../../upload/logo.png" alt="Logo" class="logo register">-->
        </div>

        <form id="auth-form" action="<?php echo $formAction; ?>" method="post">
            <h1 id="form-title">Sign In</h1>

            <!-- Hidden input to track form mode -->
            <input type="hidden" id="form-action" name="form_action" value="<?php echo $initialMode; ?>">

            <div class="input-group signup-only">
                <input type="text" placeholder="First Name" name="fname" value="<?php echo isset($_GET['fname']) ? htmlspecialchars($_GET['fname']) : ''; ?>" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_fname'): ?>
                    <span class="error-message">Please enter your first name.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <input type="text" placeholder="Last Name" name="lname" value="<?php echo isset($_GET['lname']) ? htmlspecialchars($_GET['lname']) : ''; ?>" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_lname'): ?>
                    <span class="error-message">Please enter your last name.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <input type="date" placeholder="Date Of Birth" name="dob" value="<?php echo isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : ''; ?>" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_dob'): ?>
                    <span class="error-message">Please enter your date of birth.</span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'age_requirement'): ?>
                    <span class="error-message">You must be at least 10 years old.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <input type="text" placeholder="JMBG" name="jmbg" value="<?php echo isset($_GET['jmbg']) ? htmlspecialchars($_GET['jmbg']) : ''; ?>" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_jmbg'): ?>
                    <span class="error-message">JMBG must contain exactly 13 digits.</span>
                <?php elseif (isset($_GET['error']) && str_contains($_GET['error'], 'JMBG')): ?>
                    <span class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <input type="email" placeholder="Email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_email'): ?>
                    <span class="error-message">Please enter a valid email address.</span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <input type="password" placeholder="Password" name="psw" required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_password'): ?>
                    <span class="error-message">Password must be at least 8 characters long and include an uppercase letter, a number, and a special character.</span>
                <?php endif; ?>
            </div>

            <p class="forgot-password">Lost password? <a href="#">Click Here!</a></p>
            <button type="submit" class="primary-btn submitBtn">Submit</button>
            <div class="button-group">
                <button type="button" id="signup-btn" class="<?php echo ($initialMode === 'signup') ? 'primary-btn' : 'secondary-btn'; ?>">Sign up</button>
                <button type="button" id="signin-btn" class="<?php echo ($initialMode === 'signin') ? 'primary-btn' : 'secondary-btn'; ?>">Sign in</button>
            </div>
        </form>
    </div>
</section>
<script src="../js/auth.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const url = new URL(window.location.href);
        if (url.searchParams.has('error')) {
            // Remove the error from the URL without reloading the page
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : ''));
        }
    });
</script>

</body>
</html>