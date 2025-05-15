document.addEventListener('DOMContentLoaded', function() {
    const signinBtn = document.getElementById('signin-btn');
    const signupBtn = document.getElementById('signup-btn');
    const formAction = document.getElementById('form-action');
    const formTitle = document.getElementById('form-title');
    const signupFields = document.querySelectorAll('.signup-only');
    const form = document.getElementById('auth-form');

    // Toggle between signup and signin modes
    signupBtn.addEventListener('click', function() {
        document.body.classList.remove('signin-mode');
        document.body.classList.add('signup-mode');
        formAction.value = 'signup';
        formTitle.textContent = 'Sign Up';
        signupBtn.classList.add('primary-btn');
        signupBtn.classList.remove('secondary-btn');
        signinBtn.classList.add('secondary-btn');
        signinBtn.classList.remove('primary-btn');

        // Show signup fields and make them required
        signupFields.forEach(field => {
            field.style.display = 'block';
            const inputs = field.querySelectorAll('input');
            inputs.forEach(input => input.required = true);
        });
    });

    signinBtn.addEventListener('click', function() {
        document.body.classList.remove('signup-mode');
        document.body.classList.add('signin-mode');
        formAction.value = 'signin';
        formTitle.textContent = 'Sign In';
        signinBtn.classList.add('primary-btn');
        signinBtn.classList.remove('secondary-btn');
        signupBtn.classList.add('secondary-btn');
        signupBtn.classList.remove('primary-btn');

        // Hide signup fields and make them not required
        signupFields.forEach(field => {
            field.style.display = 'none';
            const inputs = field.querySelectorAll('input');
            inputs.forEach(input => input.required = false);
        });
    });

    // Initialize based on initial mode
    if (formAction.value === 'signup') {
        signupBtn.click();
    } else {
        signinBtn.click();
    }
});