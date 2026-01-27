(function() {
    'use strict';
    
    function initForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initForm, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            const form = $('#supplierForm');
            
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
                
                // Validate address1
                const address1 = form.find('[name="address1"]').val().trim();
                if (!address1) {
                    showFieldError(form.find('[name="address1"]'), 'Address is required.');
                    isValid = false;
                }
                
                // Validate city
                const city = form.find('[name="city"]').val().trim();
                if (!city) {
                    showFieldError(form.find('[name="city"]'), 'City is required.');
                    isValid = false;
                }
                
                // Validate contact1
                const contact1 = form.find('[name="contact1"]').val().trim();
                if (!contact1) {
                    showFieldError(form.find('[name="contact1"]'), 'Contact number is required.');
                    isValid = false;
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
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();

