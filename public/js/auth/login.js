(function() {
    'use strict';
    
    $(document).ready(function() {
        const loginForm = $('#loginForm');
        const otpForm = $('#otpForm');
        
        // Auto-focus and format OTP input
        if (otpForm.length) {
            const otpInput = otpForm.find('#otp');
            otpInput.on('input', function() {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
        
        // Frontend validation for login form
        if (loginForm.length) {
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
        }
        
        // Frontend validation for OTP form
        if (otpForm.length) {
            otpForm.on('submit', function(e) {
                let isValid = true;
                
                // Reset previous errors
                otpForm.find('.is-invalid').removeClass('is-invalid');
                otpForm.find('.invalid-feedback').remove();
                
                // Validate OTP
                const otp = otpForm.find('[name="otp"]').val().trim();
                if (!otp) {
                    showFieldError(otpForm.find('[name="otp"]'), 'OTP is required.');
                    isValid = false;
                } else if (!/^\d{6}$/.test(otp)) {
                    showFieldError(otpForm.find('[name="otp"]'), 'OTP must be exactly 6 digits.');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
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

