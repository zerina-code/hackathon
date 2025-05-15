<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../../css/registration.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <title>Registracija</title>
</head>
<body class="signin-mode">
<section id="registration-section">
    <?php
    $formAction = "../../php/registration-process.php";
    $initialMode = isset($_GET['mode']) ? $_GET['mode'] : 'signin';
    ?>
    <div class="form-container">
        <div class="imgcontainer">
<!--            <img src="../../upload/logo.png" alt="Logo" class="logo register">-->
        </div>

        <form id="auth-form" action="<?php echo $formAction; ?>" method="post">
            <h1><?php echo ($initialMode === 'signup') ? 'Sign Up' : 'Sign In'; ?></h1>

            <!-- Hidden input to track form mode -->
            <input type="hidden" id="form-action" name="form_action" value="<?php echo $initialMode; ?>">

            <div class="input-group username-field" <?php echo ($initialMode === 'signin') ? 'style="display:none;"' : ''; ?>>
                <input type="text" placeholder="Username" name="username" required>
                <span class="icon user-icon"></span>
            </div>

            <div class="input-group">
                <input type="email" placeholder="Email" name="email" required>
                <span class="icon email-icon"></span>
            </div>

            <div class="input-group">
                <input type="password" placeholder="Password" name="psw" required>
                <span class="icon password-icon"></span>
            </div>

            <p class="forgot-password">Lost password? <a href="#">Click Here!</a></p>
            <button type="submit" class="primary-btn submitBtn">Submit</button>
            <div class="button-group">
                <button type="button" id="signup-btn" class="<?php echo ($initialMode === 'signup') ? 'primary-btn' : 'secondary-btn'; ?>">Sign up</button>
                <button type="submit" id="signin-btn" class="<?php echo ($initialMode === 'signin') ? 'primary-btn' : 'secondary-btn'; ?>">Sign in</button>
            </div>
        </form>
    </div>
</section>
<script src="../../js/auth.js"></script>
</body>
</html>