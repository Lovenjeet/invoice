(function() {
    'use strict';
    
    function initForm() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initForm, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            let currentStep = 1;
            const totalSteps = 5;
            const form = $('#deliveryBoyForm');
            
            // Initialize step visibility
            updateStepVisibility();
            
            // Next button handler
            $('#nextBtn').on('click', function() {
                if (validateCurrentStep()) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateStepVisibility();
                        updateWizardProgress();
                        scrollToTop();
                    }
                }
            });
            
            // Previous button handler
            $('#prevBtn').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepVisibility();
                    updateWizardProgress();
                    scrollToTop();
                }
            });
            
            // Payment type change handler
            $('select[name="payment_type"]').on('change', function() {
                const paymentType = $(this).val();
                const salaryGroup = $('#salaryAmountGroup');
                const salaryInput = $('input[name="salary"]');
                
                if (paymentType === 'Salary') {
                    salaryGroup.show();
                    salaryInput.prop('required', true);
                } else {
                    salaryGroup.hide();
                    salaryInput.prop('required', false);
                    salaryInput.val('');
                }
            });
            
            // Trigger payment type change on load
            $('select[name="payment_type"]').trigger('change');
            
            // IFSC code uppercase
            $('input[name="ifsc_code"]').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
            
            // Phone number validation (only digits)
            $('input[name="phone"], input[name="alternate_contact_number"]').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });
            
            // Aadhar and zipcode validation (only digits)
            $('input[name="aadhar_number"], input[name="zipcode"]').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });
            
            // File preview handlers
            $('#adhaar_card_upload, #driving_license_upload').on('change', function() {
                const file = this.files[0];
                const previewId = $(this).attr('id') === 'adhaar_card_upload' ? '#adhaarPreview' : '#licensePreview';
                showFilePreview(file, previewId);
            });
            
            $('#driver_photo_upload').on('change', function() {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photoPreview').html(`
                            <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Form submission handler
            form.on('submit', function(e) {
                if (!validateCurrentStep()) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            });
            
            function validateCurrentStep() {
                let isValid = true;
                const currentPane = $(`#step${currentStep}`);
                
                // Remove previous validation errors
                currentPane.find('.is-invalid').removeClass('is-invalid');
                currentPane.find('.invalid-feedback').remove();
                
                // Get all required fields in current step
                const requiredFields = currentPane.find('[required]');
                
                requiredFields.each(function() {
                    const field = $(this);
                    const value = field.val();
                    const fieldName = field.attr('name');
                    
                    // Skip password confirmation if password is empty (for edit)
                    if (fieldName === 'password_confirmation' && !$('input[name="password"]').val()) {
                        return true;
                    }
                    
                    // Skip salary if payment type is Commission
                    if (fieldName === 'salary' && $('select[name="payment_type"]').val() !== 'Salary') {
                        return true;
                    }
                    
                    if (!value || value.trim() === '') {
                        showFieldError(field, 'This field is required.');
                        isValid = false;
                        return false;
                    }
                    
                    // Additional validations
                    if (fieldName === 'phone' && value.length !== 10) {
                        showFieldError(field, 'Mobile number must be exactly 10 digits.');
                        isValid = false;
                        return false;
                    }
                    
                    if (fieldName === 'aadhar_number' && value.length !== 12) {
                        showFieldError(field, 'Aadhar number must be exactly 12 digits.');
                        isValid = false;
                        return false;
                    }
                    
                    if (fieldName === 'zipcode' && value.length !== 6) {
                        showFieldError(field, 'Zipcode must be exactly 6 digits.');
                        isValid = false;
                        return false;
                    }
                    
                    if (fieldName === 'password' && value.length < 8) {
                        showFieldError(field, 'Password must be at least 8 characters.');
                        isValid = false;
                        return false;
                    }
                    
                    if (fieldName === 'password_confirmation') {
                        const password = $('input[name="password"]').val();
                        if (password && value !== password) {
                            showFieldError(field, 'Password confirmation does not match.');
                            isValid = false;
                            return false;
                        }
                    }
                    
                    if (fieldName === 'ifsc_code' && !/^[A-Z]{4}0[A-Z0-9]{6}$/.test(value)) {
                        showFieldError(field, 'IFSC code format is invalid.');
                        isValid = false;
                        return false;
                    }
                });
                
                if (!isValid) {
                    // Scroll to first error
                    const firstError = currentPane.find('.is-invalid').first();
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
            
            function updateStepVisibility() {
                // Hide all steps
                $('.tab-pane').removeClass('show active');
                
                // Show current step
                $(`#step${currentStep}`).addClass('show active');
                
                // Update navigation buttons
                if (currentStep === 1) {
                    $('#prevBtn').hide();
                } else {
                    $('#prevBtn').show();
                }
                
                if (currentStep === totalSteps) {
                    $('#nextBtn').hide();
                    $('#submitBtn').show();
                } else {
                    $('#nextBtn').show();
                    $('#submitBtn').hide();
                }
            }
            
            function updateWizardProgress() {
                $('.wizard-step').each(function() {
                    const stepNum = parseInt($(this).data('step'));
                    const stepCircle = $(this).find('.wizard-step-circle');
                    const stepIcon = $(this).find('.wizard-step-icon');
                    const stepTitle = $(this).find('.wizard-step-title');
                    
                    if (stepNum < currentStep) {
                        // Completed step
                        $(this).addClass('completed');
                        stepCircle.removeClass('active').addClass('completed');
                        stepIcon.removeClass('text-secondary').addClass('text-success');
                        stepTitle.removeClass('text-secondary').addClass('text-success');
                    } else if (stepNum === currentStep) {
                        // Current step
                        $(this).addClass('active');
                        stepCircle.addClass('active').removeClass('completed');
                        stepIcon.removeClass('text-secondary text-success').addClass('text-primary');
                        stepTitle.removeClass('text-secondary text-success').addClass('text-primary fw-semibold');
                    } else {
                        // Future step
                        $(this).removeClass('active completed');
                        stepCircle.removeClass('active completed');
                        stepIcon.removeClass('text-primary text-success').addClass('text-secondary');
                        stepTitle.removeClass('text-primary text-success fw-semibold').addClass('text-secondary');
                    }
                });
            }
            
            function scrollToTop() {
                $('html, body').animate({
                    scrollTop: $('.wizard-progress').offset().top - 50
                }, 300);
            }
            
            function showFilePreview(file, previewId) {
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $(previewId).html(`
                                <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            `);
                        };
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        $(previewId).html(`
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF File: ${file.name}
                            </div>
                        `);
                    }
                }
            }
            
            // Initialize wizard progress
            updateWizardProgress();
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();

