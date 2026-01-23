(function() {
    'use strict';
    
    $(document).ready(function() {
        const loginForm = $('#loginForm');
        
        // Frontend validation
        loginForm.on('submit', function(e) {
            let isValid = true;
            
            // Reset previous errors
            loginForm.find('.is-invalid').removeClass('is-invalid');
            loginForm.find('.invalid-feedback').remove();
            
            // Validate email
            const email = loginForm.find('[name="email"]').val().trim();
            if (!email) {
                showFieldError(loginForm.find('[name="email"]'), 'Email is required.');
                isValid = false;
            } else if (!isValidEmail(email)) {
                showFieldError(loginForm.find('[name="email"]'), 'Please enter a valid email address.');
                isValid = false;
            }
            
            // Validate password
            const password = loginForm.find('[name="password"]').val();
            if (!password) {
                showFieldError(loginForm.find('[name="password"]'), 'Password is required.');
                isValid = false;
            } else if (password.length < 6) {
                showFieldError(loginForm.find('[name="password"]'), 'Password must be at least 6 characters.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function showFieldError(field, message) {
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">' + message + '</div>');
        }
    });
})();

