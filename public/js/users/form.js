(function() {
    'use strict';
    
    function initForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initForm, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            const form = $('#userForm');
            
            // Form submission handler
            form.on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                // Re-enable button after 5 seconds as fallback
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }, 5000);
            });
            
            function validateForm() {
                let isValid = true;
                
                // Reset previous errors
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                
                // Validate name
                const name = form.find('[name="name"]').val().trim();
                if (!name) {
                    showFieldError(form.find('[name="name"]'), 'Name is required.');
                    isValid = false;
                }
                
                // Validate email
                const email = form.find('[name="email"]').val().trim();
                if (!email) {
                    showFieldError(form.find('[name="email"]'), 'Email is required.');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError(form.find('[name="email"]'), 'Email must be a valid email address.');
                    isValid = false;
                }
                
                // Validate password (only if creating or if password is provided)
                const password = form.find('[name="password"]').val();
                const passwordConfirmation = form.find('[name="password_confirmation"]').val();
                
                // Check if this is create or update with password
                const isCreate = form.attr('action').includes('/store');
                const isUpdateWithPassword = !isCreate && password;
                
                if (isCreate || isUpdateWithPassword) {
                    if (!password) {
                        if (isCreate) {
                            showFieldError(form.find('[name="password"]'), 'Password is required.');
                            isValid = false;
                        }
                    } else if (password.length < 8) {
                        showFieldError(form.find('[name="password"]'), 'Password must be at least 8 characters.');
                        isValid = false;
                    }
                    
                    if (password && password !== passwordConfirmation) {
                        showFieldError(form.find('[name="password_confirmation"]'), 'Password confirmation does not match.');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    // Scroll to first error
                    const firstError = form.find('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }
                
                return isValid;
            }
            
            function showFieldError(field, message) {
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback">${message}</div>`);
            }
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();

