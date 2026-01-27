(function() {
    'use strict';
    
    function initForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initForm, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            const form = $('#hsCodeForm');
            
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
                
                // Validate model
                const model = form.find('[name="model"]').val().trim();
                if (!model) {
                    showFieldError(form.find('[name="model"]'), 'Model is required.');
                    isValid = false;
                }
                
                // Validate sku
                const sku = form.find('[name="sku"]').val().trim();
                if (!sku) {
                    showFieldError(form.find('[name="sku"]'), 'SKU is required.');
                    isValid = false;
                }
                
                // Validate hs_code
                const hsCode = form.find('[name="hs_code"]').val().trim();
                if (!hsCode) {
                    showFieldError(form.find('[name="hs_code"]'), 'HS Code is required.');
                    isValid = false;
                }
                
                // Validate number_of_units
                const numberOfUnits = form.find('[name="number_of_units"]').val().trim();
                if (!numberOfUnits) {
                    showFieldError(form.find('[name="number_of_units"]'), 'Number of units is required.');
                    isValid = false;
                } else if (isNaN(numberOfUnits)) {
                    showFieldError(form.find('[name="number_of_units"]'), 'Number of units must be a number.');
                    isValid = false;
                }
                
                // Validate weight
                const weight = form.find('[name="weight"]').val().trim();
                if (!weight) {
                    showFieldError(form.find('[name="weight"]'), 'Weight is required.');
                    isValid = false;
                } else if (isNaN(weight)) {
                    showFieldError(form.find('[name="weight"]'), 'Weight must be a number.');
                    isValid = false;
                } else if (parseFloat(weight) < 0 || parseFloat(weight) > 999999.99) {
                    showFieldError(form.find('[name="weight"]'), 'Weight must be between 0 and 999999.99.');
                    isValid = false;
                }
                
                // DG is optional, no validation needed
                
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

