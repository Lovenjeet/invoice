(function() {
    'use strict';
    
    $(document).ready(function() {
        const productForm = $('#productForm');
        
        // Frontend validation
        productForm.on('submit', function(e) {
            let isValid = true;
            
            // Reset previous errors
            productForm.find('.is-invalid').removeClass('is-invalid');
            productForm.find('.invalid-feedback').remove();
            
            // Validate name
            const name = productForm.find('[name="name"]').val().trim();
            if (!name) {
                showFieldError(productForm.find('[name="name"]'), 'Product name is required.');
                isValid = false;
            } else if (name.length < 3) {
                showFieldError(productForm.find('[name="name"]'), 'Product name must be at least 3 characters.');
                isValid = false;
            }
            
            // Validate price
            const price = parseFloat(productForm.find('[name="price"]').val());
            if (!productForm.find('[name="price"]').val() || isNaN(price)) {
                showFieldError(productForm.find('[name="price"]'), 'Price is required and must be a valid number.');
                isValid = false;
            } else if (price < 0) {
                showFieldError(productForm.find('[name="price"]'), 'Price must be greater than or equal to 0.');
                isValid = false;
            }
            
            // Validate status
            const status = productForm.find('[name="status"]').val();
            if (!status) {
                showFieldError(productForm.find('[name="status"]'), 'Status is required.');
                isValid = false;
            } else if (!['active', 'inactive'].includes(status)) {
                showFieldError(productForm.find('[name="status"]'), 'Status must be either active or inactive.');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = productForm.find('button[type="submit"]');
            submitBtn.prop('disabled', true);
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        });
        
        function showFieldError(field, message) {
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">' + message + '</div>');
        }
    });
})();

