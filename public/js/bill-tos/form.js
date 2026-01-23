(function() {
    'use strict';
    
    function initForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initForm, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            const form = $('#billToForm');
            
            form.on('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    return false;
                }
                
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                const originalText = submitBtn.html();
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }, 5000);
            });
            
            function validateForm() {
                let isValid = true;
                
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').remove();
                
                const name = form.find('[name="name"]').val().trim();
                if (!name) {
                    showFieldError(form.find('[name="name"]'), 'Name is required.');
                    isValid = false;
                }
                
                const address1 = form.find('[name="address1"]').val().trim();
                if (!address1) {
                    showFieldError(form.find('[name="address1"]'), 'Address is required.');
                    isValid = false;
                }
                
                const city = form.find('[name="city"]').val().trim();
                if (!city) {
                    showFieldError(form.find('[name="city"]'), 'City is required.');
                    isValid = false;
                }
                
                if (!isValid) {
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

