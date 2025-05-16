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

    // Start session to access any session error messages
    session_start();
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
                <label for="fname">First Name</label>
                <input type="text" id="fname" placeholder="First Name" name="fname"
                       value="<?php echo isset($_GET['fname']) ? htmlspecialchars($_GET['fname']) : ''; ?>"
                       class="<?php echo isset($_GET['error']) && $_GET['error'] === 'missing_fname' ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_fname'): ?>
                    <span class="error-message">Please enter your first name.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" placeholder="Last Name" name="lname"
                       value="<?php echo isset($_GET['lname']) ? htmlspecialchars($_GET['lname']) : ''; ?>"
                       class="<?php echo isset($_GET['error']) && $_GET['error'] === 'missing_lname' ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_lname'): ?>
                    <span class="error-message">Please enter your last name.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <label for="dob">Date Of Birth</label>
                <input type="date" id="dob" placeholder="Date Of Birth" name="dob"
                       value="<?php echo isset($_GET['dob']) ? htmlspecialchars($_GET['dob']) : ''; ?>"
                       class="<?php echo isset($_GET['error']) && ($_GET['error'] === 'missing_dob' || $_GET['error'] === 'age_requirement') ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_dob'): ?>
                    <span class="error-message">Please enter your date of birth.</span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'age_requirement'): ?>
                    <span class="error-message">You must be at least 10 years old.</span>
                <?php endif; ?>
            </div>

            <div class="input-group signup-only">
                <label for="jmbg">JMBG</label>
                <input type="text" id="jmbg" placeholder="JMBG" name="jmbg"
                       value="<?php echo isset($_GET['jmbg']) ? htmlspecialchars($_GET['jmbg']) : ''; ?>"
                       class="<?php echo isset($_GET['error']) && (str_contains($_GET['error'], 'jmbg') || str_contains($_GET['error'], 'JMBG')) ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_jmbg'): ?>
                    <span class="error-message">JMBG must contain exactly 13 digits.</span>
                <?php elseif (isset($_GET['error']) && str_contains($_GET['error'], 'JMBG')): ?>
                    <span class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Email" name="email"
                       value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
                       class="<?php echo isset($_GET['error']) && ($_GET['error'] === 'invalid_email' || $_GET['error'] === 'user_not_found') ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_email'): ?>
                    <span class="error-message">Please enter a valid email address.</span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'user_not_found'): ?>
                    <span class="error-message">No account found with this email.</span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label for="psw">Password</label>
                <input type="password" id="psw" placeholder="Password" name="psw"
                       class="<?php echo isset($_GET['error']) && ($_GET['error'] === 'invalid_password' || $_GET['error'] === 'invalid_credentials') ? 'input-error' : ''; ?>"
                       required>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_password'): ?>
                    <span class="error-message">
                        <?php
                        if(isset($_SESSION['password_error'])) {
                            echo $_SESSION['password_error'];
                            unset($_SESSION['password_error']); // Clear after displaying
                        } else {
                            echo "Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character.";
                        }
                        ?>
                    </span>
                <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                    <span class="error-message">Incorrect password. Please try again.</span>
                <?php endif; ?>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_fields'): ?>
                <span class="error-message">Please fill in all required fields.</span>
            <?php endif; ?>

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

        // Display form in right mode based on URL params
        const formAction = document.getElementById('form-action').value;
        if (formAction === 'signup') {
            document.body.classList.remove('signin-mode');
            document.body.classList.add('signup-mode');
            document.getElementById('form-title').textContent = 'Sign Up';
        }

        // Ensure error messages are fully visible by adjusting their width
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(message => {
            message.style.display = 'block';
            message.style.whiteSpace = 'normal';
            message.style.wordBreak = 'break-word';
        });

        // Remove error params from URL after displaying them
        if (url.searchParams.has('error')) {
            // Remove the error from the URL without reloading the page
            setTimeout(() => {
                url.searchParams.delete('error');
                window.history.replaceState({}, document.title, url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : ''));
            }, 5000); // Remove after 5 seconds
        }
    });
</script>

</body>
</html>