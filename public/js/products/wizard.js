(function() {
    'use strict';
    
    // Wait for jQuery to be available
    function initWizard() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initWizard, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        let currentStep = 1;
        const totalSteps = 3;
        let variantIndex = $('.variant-row').length > 0 ? Math.max(...$('.variant-row').map(function() { return parseInt($(this).data('index')); }).get()) + 1 : 0;
        
        $(document).ready(function() {
            // Step navigation
            $('#nextBtn').on('click', function() {
                const $btn = $(this);
                
                // For Step 1, check name uniqueness asynchronously
                if (currentStep === 1) {
                    const originalBtnHtml = $btn.html();
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Checking...');
                    
                    validateStepAsync(currentStep).then(function(isValid) {
                        $btn.prop('disabled', false).html(originalBtnHtml);
                        
                        if (isValid) {
                            if (currentStep < totalSteps) {
                                currentStep++;
                                updateStepDisplay();
                            }
                        }
                    }).catch(function(error) {
                        console.error('Validation error:', error);
                        $btn.prop('disabled', false).html(originalBtnHtml);
                    });
                } else {
                    // For other steps, use synchronous validation
                    if (validateStep(currentStep)) {
                        if (currentStep < totalSteps) {
                            currentStep++;
                            updateStepDisplay();
                        }
                    }
                }
            });
            
            $('#prevBtn').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepDisplay();
                }
            });
            
            // Wizard step click handler - allow clicking on completed/previous steps
            $('.wizard-step').on('click', function() {
                const clickedStep = parseInt($(this).data('step'));
                // Allow going to previous steps or next step if current is valid
                if (clickedStep < currentStep) {
                    currentStep = clickedStep;
                    updateStepDisplay();
                } else if (clickedStep === currentStep + 1 && validateStep(currentStep)) {
                    currentStep = clickedStep;
                    updateStepDisplay();
                }
            });
            
            // Tab click handler - prevent manual tab switching, use buttons only
            $('[data-bs-toggle="tab"]').on('click', function(e) {
                const target = $(e.target).closest('[data-bs-toggle="tab"]').data('bs-target');
                if (!target) return;
                const clickedStep = parseInt(target.replace('#step', ''));
                // Only allow going to previous steps or if validation passes
                if (clickedStep < currentStep) {
                    currentStep = clickedStep;
                    updateStepDisplay();
                } else if (clickedStep === currentStep + 1 && validateStep(currentStep)) {
                    currentStep = clickedStep;
                    updateStepDisplay();
                } else {
                    e.preventDefault();
                    return false;
                }
            });
            
            function updateStepDisplay() {
                // Update wizard steps visual
                $('.wizard-step').each(function(index) {
                    const stepNum = index + 1;
                    const $step = $(this);
                    $step.removeClass('active completed');
                    
                    if (stepNum < currentStep) {
                        $step.addClass('completed');
                    } else if (stepNum === currentStep) {
                        $step.addClass('active');
                    }
                });
                
                // Update tab buttons (if they exist)
                $('.nav-link').removeClass('active');
                const $tabBtn = $(`#step${currentStep}-tab`);
                if ($tabBtn.length) {
                    $tabBtn.addClass('active');
                }
                
                // Update tab panes
                $('.tab-pane').removeClass('show active');
                $(`#step${currentStep}`).addClass('show active');
                
                // Update navigation buttons
                $('#prevBtn').toggle(currentStep > 1);
                $('#nextBtn').toggle(currentStep < totalSteps);
                $('#submitBtn').toggle(currentStep === totalSteps);
            }
            
            // Initialize wizard display on load
            updateStepDisplay();
            
            // Async validation for Step 1 (includes name uniqueness check)
            function validateStepAsync(step) {
                return new Promise(function(resolve, reject) {
                    if (step !== 1) {
                        resolve(validateStep(step));
                        return;
                    }
                    
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    
                    // Validate Step 1
                    const categoryId = $('#category_id').val();
                    const name = $('#name').val().trim();
                    let isValid = true;
                    
                    if (!categoryId) {
                        showFieldError($('#category_id'), 'Category is required.');
                        isValid = false;
                        resolve(false);
                        return;
                    }
                    
                    if (!name) {
                        showFieldError($('#name'), 'Product name is required.');
                        isValid = false;
                        resolve(false);
                        return;
                    } else if (name.length < 3) {
                        showFieldError($('#name'), 'Product name must be at least 3 characters.');
                        isValid = false;
                        resolve(false);
                        return;
                    }
                    
                    // Check name uniqueness via AJAX
                    const productId = $('#productForm').data('product-id') || '';
                    
                    $.ajax({
                        url: '/admin/products/check-name-unique',
                        type: 'POST',
                        data: {
                            name: name,
                            product_id: productId || null
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.unique) {
                                resolve(true);
                            } else {
                                showFieldError($('#name'), response.message || 'This product name already exists. Please choose a different name.');
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Validation Error',
                                        text: response.message || 'This product name already exists. Please choose a different name.',
                                        icon: 'error',
                                        confirmButtonColor: '#0d6efd'
                                    });
                                }
                                resolve(false);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error checking name uniqueness:', xhr);
                            let errorMsg = 'Failed to validate product name. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            showFieldError($('#name'), errorMsg);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Validation Error',
                                    text: errorMsg,
                                    icon: 'error',
                                    confirmButtonColor: '#0d6efd'
                                });
                            }
                            resolve(false);
                        }
                    });
                });
            }
            
            function validateStep(step) {
                let isValid = true;
                
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                if (step === 1) {
                    // Basic validation for Step 1 (name uniqueness checked separately)
                    const categoryId = $('#category_id').val();
                    const name = $('#name').val().trim();
                    
                    if (!categoryId) {
                        showFieldError($('#category_id'), 'Category is required.');
                        isValid = false;
                    }
                    
                    if (!name) {
                        showFieldError($('#name'), 'Product name is required.');
                        isValid = false;
                    } else if (name.length < 3) {
                        showFieldError($('#name'), 'Product name must be at least 3 characters.');
                        isValid = false;
                    }
                } else if (step === 2) {
                    // Validate Step 2 - Variants
                    const variantRows = $('.variant-row');
                    
                    if (variantRows.length === 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'At least one variant is required.',
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        }
                        isValid = false;
                    } else {
                        variantRows.each(function() {
                            const $row = $(this);
                            const size = $row.find('.variant-size').val().trim();
                            const price = parseFloat($row.find('.variant-price').val());
                            
                            if (!size) {
                                showFieldError($row.find('.variant-size'), 'Size is required.');
                                isValid = false;
                            }
                            
                            if (!$row.find('.variant-price').val() || isNaN(price)) {
                                showFieldError($row.find('.variant-price'), 'Price is required and must be a valid number.');
                                isValid = false;
                            } else if (price < 0) {
                                showFieldError($row.find('.variant-price'), 'Price must be greater than or equal to 0.');
                                isValid = false;
                            }
                        });
                    }
                }
                
                if (!isValid) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please fix the errors before proceeding.',
                            icon: 'error',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                }
                
                return isValid;
            }
            
            function showFieldError(field, message) {
                field.addClass('is-invalid');
                const feedback = field.next('.invalid-feedback');
                if (feedback.length === 0) {
                    field.after('<div class="invalid-feedback">' + message + '</div>');
                } else {
                    feedback.text(message);
                }
            }
            
            // Inline Category Add
            $('#addCategoryBtn').on('click', function() {
                $('#categoryDropdownGroup').hide();
                $('#categoryInputGroup').show();
                $('#newCategoryName').focus();
            });
            
            $('#cancelCategoryBtn').on('click', function() {
                $('#categoryInputGroup').hide();
                $('#categoryDropdownGroup').show();
                $('#newCategoryName').val('');
                $('#categoryError').hide().text('');
                $('#newCategoryName').removeClass('is-invalid');
            });
            
            $('#saveCategoryBtn').on('click', function() {
                const categoryName = $('#newCategoryName').val().trim();
                
                // Frontend validation
                if (!categoryName) {
                    $('#newCategoryName').addClass('is-invalid');
                    $('#categoryError').text('Category name is required.').show();
                    return;
                }
                
                if (categoryName.length < 2) {
                    $('#newCategoryName').addClass('is-invalid');
                    $('#categoryError').text('Category name must be at least 2 characters.').show();
                    return;
                }
                
                // Create category via AJAX
                $.ajax({
                    url: '/categories',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        name: categoryName
                    },
                    success: function(response) {
                        // Add new option to select
                        const option = $('<option>', {
                            value: response.id,
                            text: response.name
                        });
                        $('#category_id').append(option).val(response.id);
                        
                        // Hide input, show dropdown
                        $('#categoryInputGroup').hide();
                        $('#categoryDropdownGroup').show();
                        $('#newCategoryName').val('');
                        $('#categoryError').hide();
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Category added successfully.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                            errorMsg = xhr.responseJSON.errors.name[0];
                        }
                        $('#newCategoryName').addClass('is-invalid');
                        $('#categoryError').text(errorMsg).show();
                    }
                });
            });
            
            // Variants Management
            function addVariantRow() {
                const rowHtml = `
                    <tr class="variant-row" data-index="${variantIndex}" data-variant-id="">
                        <td>
                            <input 
                                type="text" 
                                class="form-control variant-size" 
                                name="variants[${variantIndex}][size]" 
                                placeholder="e.g., 500ml, 1Kg" 
                                required
                                maxlength="50"
                            >
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input 
                                type="number" 
                                class="form-control variant-price" 
                                name="variants[${variantIndex}][price]" 
                                step="0.01" 
                                min="0"
                                placeholder="0.00" 
                                required
                            >
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <input 
                                type="number" 
                                class="form-control variant-commission" 
                                name="variants[${variantIndex}][delivery_boy_commission]" 
                                step="0.01" 
                                min="0"
                                placeholder="0.00"
                            >
                            <div class="invalid-feedback"></div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary upload-variant-images" data-index="${variantIndex}" data-variant-id="">
                                    <i class="bi bi-images"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove-variant">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                
                $('#variantsTableBody').append(rowHtml);
                variantIndex++;
                updateRemoveButtons();
            }
            
            function removeVariantRow($row) {
                if ($('.variant-row').length <= 1) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Cannot Remove',
                            text: 'At least one variant is required.',
                            icon: 'warning',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                    return;
                }
                
                $row.remove();
                reindexVariants();
                updateRemoveButtons();
            }
            
            function reindexVariants() {
                $('.variant-row').each(function(index) {
                    const $row = $(this);
                    $row.attr('data-index', index);
                    $row.find('.variant-size').attr('name', `variants[${index}][size]`);
                    $row.find('.variant-price').attr('name', `variants[${index}][price]`);
                    $row.find('.variant-commission').attr('name', `variants[${index}][delivery_boy_commission]`);
                });
            }
            
            function updateRemoveButtons() {
                const rowCount = $('.variant-row').length;
                $('.remove-variant').prop('disabled', rowCount <= 1);
            }
            
            $('#addVariantBtn').on('click', function() {
                addVariantRow();
            });
            
            $(document).on('click', '.remove-variant', function() {
                const $row = $(this).closest('.variant-row');
                removeVariantRow($row);
            });
            
            // Initialize remove buttons state
            updateRemoveButtons();
            
            // Validate before final submit (handled by images.js)
            // Note: images.js handles the actual form submission, this just validates
            $(document).on('click', '#submitBtn', function(e) {
                // Only validate, don't prevent - let images.js handle submission
                if (!validateStep(totalSteps)) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Go to the step with errors
                    if (!$('#category_id').val() || !$('#name').val().trim()) {
                        currentStep = 1;
                    } else if ($('.variant-row').length === 0) {
                        currentStep = 2;
                    }
                    updateStepDisplay();
                    return false;
                }
                // If validation passes, let images.js handle the submission via AJAX
                // Don't return false here - allow images.js handler to execute
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWizard);
    } else {
        initWizard();
    }
})();
