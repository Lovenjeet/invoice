(function() {
    'use strict';
    
    
    // Wait for jQuery and dependencies to be available
    function initImages() {
        
        if (typeof jQuery === 'undefined') {
            setTimeout(initImages, 100);
            return;
        }
        const $ = window.jQuery || jQuery;
        
        let productImages = [];
        let commonImages = []; // Store files for common images
        
        function previewImage(file, containerId) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="col-md-3 mb-3" data-file-name="${file.name}">
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate">${file.name}</small>
                                <button type="button" class="btn btn-sm btn-danger mt-2 remove-image" data-file-name="${file.name}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $(`#${containerId}`).append(previewHtml);
            };
            reader.readAsDataURL(file);
        }
        
        function removePreview(fileName, containerId) {
            $(`#${containerId} [data-file-name="${fileName}"]`).remove();
        }
        
        function previewImageFromUrl(imageUrl, fileName, imageId, containerId) {
            // For existing images in edit mode
            const previewHtml = `
                <div class="col-md-3 mb-3" data-existing-image-id="${imageId}" data-file-name="${fileName}">
                    <div class="card position-relative">
                        <img src="${imageUrl}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate" title="${fileName}">${fileName}</small>
                            <button type="button" class="btn btn-sm btn-danger mt-2 remove-existing-common-image" data-image-id="${imageId}" data-file-name="${fileName}">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $(`#${containerId}`).append(previewHtml);
        }
        
        // Load existing common images for editing
        function loadExistingCommonImages(productId) {
            console.log('loadExistingCommonImages called with productId:', productId);
            const $previewContainer = $('#commonImagesPreview');
            console.log('Preview container found:', $previewContainer.length > 0);
            
            $.ajax({
                url: `/admin/products/${productId}/common-images`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Common images API response:', response);
                    if (response.images && response.images.length > 0) {
                        console.log('Loading existing common images:', response.images.length);
                        response.images.forEach(function(image) {
                            console.log('Processing image:', image);
                            if (image.url) {
                                // Display preview for existing images
                                previewImageFromUrl(image.url, image.file_name || 'image-' + image.id, image.id, 'commonImagesPreview');
                            } else {
                                console.warn('Image has no URL:', image);
                            }
                        });
                        console.log('Finished loading common images. Preview container now has', $('#commonImagesPreview [data-existing-image-id]').length, 'images');
                    } else {
                        console.log('No common images found in response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load existing common images:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        error: error,
                        responseText: xhr.responseText
                    });
                }
            });
        }
        
        // Handle removal of existing common images (for edit mode)
        $(document).on('click', '.remove-existing-common-image', function() {
            const imageId = $(this).data('image-id');
            const fileName = $(this).data('file-name');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Image?',
                    text: 'Are you sure you want to remove this image?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Remove from preview
                        $(`#commonImagesPreview [data-existing-image-id="${imageId}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Delete from server
                        $.ajax({
                            url: `/admin/product-images/${imageId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                console.log('Common image deleted from server');
                            },
                            error: function() {
                                console.error('Failed to delete common image from server');
                            }
                        });
                    }
                });
            } else {
                if (confirm('Are you sure you want to remove this image?')) {
                    $(`#commonImagesPreview [data-existing-image-id="${imageId}"]`).remove();
                }
            }
        });
        
        // Handle removal of new common images (files not yet uploaded)
        $(document).on('click', '.remove-common-image', function() {
            const fileName = $(this).data('file-name');
            
            // Remove from preview
            $(`#commonImagesPreview [data-file-name="${fileName}"]`).remove();
            
            // Remove from commonImages array
            commonImages = commonImages.filter(f => {
                const fileToCheck = f.file || f;
                return fileToCheck.name !== fileName;
            });
        });
        
        function setupFallbackFileInputs() {
            // Create file inputs if Dropzone is not available
            if ($('#productImagesInput').length === 0) {
                $('#productImagesDropzone').html(`
                    <input type="file" id="productImagesInput" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="form-control">
                `);
            }
            
            if ($('#commonImagesInput').length === 0) {
                $('#commonImagesDropzone').html(`
                    <input type="file" id="commonImagesInput" name="common_images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="form-control">
                `);
            }
        }
        
        $(document).ready(function() {
            console.log("================================================");
        // Initialize Dropzone for Product Images (only if element exists)
        if (typeof Dropzone !== 'undefined') {
            // Check if Product Images Dropzone element exists
            if ($('#productImagesDropzone').length > 0) {
                const productDropzone = new Dropzone('#productImagesDropzone', {
                url: '#',
                autoProcessQueue: false,
                addRemoveLinks: true,
                maxFilesize: 2,
                acceptedFiles: 'image/jpeg,image/jpg,image/png,image/webp',
                dictDefaultMessage: '<i class="bi bi-cloud-upload" style="font-size: 3rem;"></i><br>Drag & drop images here or click to upload',
                dictRemoveFile: 'Remove',
                dictInvalidFileType: 'Invalid file type. Only JPG, PNG, and WEBP are allowed.',
                dictFileTooBig: 'File is too big. Maximum size is 2MB.',
                init: function() {
                    const dzInstance = this;
                    
                    dzInstance.on('addedfile', function(file) {
                        productImages.push(file);
                        previewImage(file, 'productImagesPreview');
                    });
                    
                    dzInstance.on('removedfile', function(file) {
                        productImages = productImages.filter(f => f !== file);
                        removePreview(file.name, 'productImagesPreview');
                    });
                }
            });
            } else {
                console.log('Product Images Dropzone element not found, skipping initialization');
            }
            
            // Initialize Dropzone for Common Images (only if element exists)
            if ($('#commonImagesDropzone').length > 0) {
                const commonDropzone = new Dropzone('#commonImagesDropzone', {
                url: '#',
                autoProcessQueue: false,
                addRemoveLinks: true,
                maxFilesize: 2,
                acceptedFiles: 'image/jpeg,image/jpg,image/png,image/webp',
                dictDefaultMessage: '<i class="bi bi-cloud-upload" style="font-size: 3rem;"></i><br>Drag & drop images here or click to upload',
                dictRemoveFile: 'Remove',
                dictInvalidFileType: 'Invalid file type. Only JPG, PNG, and WEBP are allowed.',
                dictFileTooBig: 'File is too big. Maximum size is 2MB.',
                clickable: true, // Enable Dropzone's built-in click functionality
                init: function() {
                    const dzInstance = this;
                    
                    console.log('Common Images Dropzone initialized, element:', dzInstance.element);
                    console.log('Common Images Dropzone clickable:', dzInstance.clickable);
                    
                    dzInstance.on('addedfile', function(file) {
                        // Store file for upload on form submit
                        commonImages.push(file);
                        
                        // Show preview
                        previewImage(file, 'commonImagesPreview');
                    });
                    
                    dzInstance.on('removedfile', function(file) {
                        // Remove from arrays
                        commonImages = commonImages.filter(f => {
                            const fileToCheck = f.file || f;
                            return fileToCheck !== file && fileToCheck.name !== file.name;
                        });
                        removePreview(file.name, 'commonImagesPreview');
                    });
                    
                    // Dropzone handles clicks natively when clickable: true is set
                    // No custom click handlers needed - they cause double file picker opening
                    if (dzInstance.element) {
                        dzInstance.element.style.cursor = 'pointer';
                    }
                }
            });
            
            // Load existing common images if editing
            function loadCommonImagesIfNeeded() {
                const $commonDropzoneElement = $('#commonImagesDropzone');
                if ($commonDropzoneElement.length > 0) {
                    const productId = $commonDropzoneElement.data('product-id');
                    console.log('Common Images Dropzone found, productId:', productId);
                    if (productId && productId !== '' && productId !== null && productId !== undefined) {
                        // Check if images are already loaded
                        const existingImagesCount = $('#commonImagesPreview [data-existing-image-id]').length;
                        if (existingImagesCount === 0) {
                            // Load after a short delay to ensure DOM is ready
                            console.log('Loading existing common images for product:', productId);
                            loadExistingCommonImages(productId);
                        } else {
                            console.log('Common images already loaded, count:', existingImagesCount);
                        }
                    } else {
                        console.log('No productId found for common images, skipping load');
                    }
                }
            }
            
            // Load on page load
            setTimeout(function() {
                loadCommonImagesIfNeeded();
            }, 1000);
            
            // Also load when Step 3 becomes active (when user navigates to it via wizard)
            // Listen for when step3 pane becomes visible
            const checkStep3Visibility = function() {
                const $step3 = $('#step3');
                if ($step3.length > 0 && $step3.hasClass('show') && $step3.hasClass('active')) {
                    const existingImagesCount = $('#commonImagesPreview [data-existing-image-id]').length;
                    if (existingImagesCount === 0) {
                        console.log('Step 3 is now visible, loading common images');
                        loadCommonImagesIfNeeded();
                    }
                }
            };
            
            // Check when wizard steps change (listen for DOM changes on step3)
            if (typeof MutationObserver !== 'undefined') {
                const $step3 = $('#step3');
                if ($step3.length > 0) {
                    const observer = new MutationObserver(function(mutations) {
                        checkStep3Visibility();
                    });
                    observer.observe($step3[0], {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                }
            }
            
            // Also check periodically as fallback
            setInterval(checkStep3Visibility, 2000);
            
            // Also listen for Bootstrap tab events (if tabs are used)
            $(document).on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#step3' || $(e.target).data('bs-target') === '#step3') {
                    console.log('Step 3 tab shown via Bootstrap, loading common images');
                    loadCommonImagesIfNeeded();
                }
            });
            
            // Listen for wizard navigation buttons (next/prev)
            $(document).on('click', '#nextBtn, #prevBtn', function() {
                setTimeout(function() {
                    checkStep3Visibility();
                }, 500);
            });
            
            // Also check immediately when step3 becomes visible
            const $step3 = $('#step3');
            if ($step3.length > 0) {
                // Use IntersectionObserver to detect when step3 becomes visible
                if (typeof IntersectionObserver !== 'undefined') {
                    const io = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting && entry.target.classList.contains('show')) {
                                console.log('Step 3 is now visible (IntersectionObserver), loading common images');
                                setTimeout(function() {
                                    loadCommonImagesIfNeeded();
                                }, 300);
                            }
                        });
                    }, { threshold: 0.1 });
                    io.observe($step3[0]);
                }
            }
            
            } else {
                console.log('Common Images Dropzone element not found, skipping initialization');
            }
            
            // Prevent native form submission - handle via AJAX only
            $('#productForm').on('submit', function(e) {
                console.log('Form submit event triggered - preventing default');
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
            
            // Handle form submission via submit button
            // Attach with higher priority by using setTimeout to ensure it runs after wizard.js
            setTimeout(function() {
                $(document).on('click', '#submitBtn', function(e) {
                    console.log('Submit button clicked - starting AJAX submission');
                    
                    // Check if wizard validation passed (wizard.js runs first)
                    // If validation failed, wizard.js would have already prevented default
                    // So if we get here, validation passed - proceed with AJAX
                    
                    e.preventDefault();
                    e.stopPropagation();
                
                const form = $('#productForm');
                
                // Validate form before submission
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return false;
                }
                
                // Ensure variant IDs are present in the form before creating FormData
                $('#variantsTableBody .variant-row').each(function() {
                    const index = $(this).data('index');
                    const variantId = $(this).data('variant-id');
                    
                    // Ensure hidden input exists for variant ID if variant exists
                    if (variantId && index !== undefined && index !== null) {
                        const variantIdInput = $(this).find(`input[name="variants[${index}][id]"]`);
                        if (variantIdInput.length === 0) {
                            // Add hidden input if it doesn't exist
                            const hiddenInput = $('<input>', {
                                type: 'hidden',
                                name: `variants[${index}][id]`,
                                value: variantId
                            });
                            $(this).prepend(hiddenInput);
                        } else {
                            // Ensure the value is correct
                            variantIdInput.val(variantId);
                        }
                    }
                });
                
                const formData = new FormData(form[0]);
                
                // Append variant images as files
                const formVariantIndices = [];
                $('#variantsTableBody .variant-row').each(function() {
                    const index = $(this).data('index');
                    if (index !== undefined && index !== null) {
                        formVariantIndices.push(String(index));
                    }
                });
                
                console.log('=== Form Submission Debug ===');
                console.log('Form variant indices:', formVariantIndices);
                console.log('window.variantImages:', window.variantImages);
                
                // Debug: Log variant IDs being sent
                console.log('=== Variant IDs Debug ===');
                $('#variantsTableBody .variant-row').each(function() {
                    const index = $(this).data('index');
                    const variantId = $(this).data('variant-id');
                    const hiddenInput = $(this).find(`input[name="variants[${index}][id]"]`);
                    console.log(`Variant index ${index}: variantId=${variantId}, hiddenInput value=${hiddenInput.val()}`);
                });
                
                // Debug: Log FormData entries for variant IDs
                console.log('=== FormData Variant IDs ===');
                for (let pair of formData.entries()) {
                    if (pair[0].includes('variants') && pair[0].includes('[id]')) {
                        console.log(`FormData: ${pair[0]} = ${pair[1]}`);
                    }
                }
                
                let totalFilesAppended = 0;
                
                if (window.variantImages && typeof window.variantImages === 'object') {
                    formVariantIndices.forEach(function(variantIndex) {
                        const indexKey = String(variantIndex);
                        const files = window.variantImages[indexKey] || [];
                        console.log(`Variant ${indexKey}: ${files.length} files`);
                        
                        if (files.length > 0 && Array.isArray(files)) {
                            files.forEach(function(file, fileIndex) {
                                // Ensure it's a File object
                                let actualFile = file;
                                if (file && typeof file === 'object' && file.file instanceof File) {
                                    actualFile = file.file;
                                }
                                
                                if (actualFile instanceof File || actualFile instanceof Blob) {
                                    const formKey = `variant_images_${indexKey}[]`;
                                    formData.append(formKey, actualFile);
                                    totalFilesAppended++;
                                    console.log(`✓ Appended file: ${formKey} -> ${actualFile.name} (${actualFile.size} bytes)`);
                                } else {
                                    console.error(`✗ Failed: File ${fileIndex} is not a File/Blob:`, actualFile);
                                }
                            });
                        }
                    });
                }
                
                console.log(`Total files appended to FormData: ${totalFilesAppended}`);
                
                // Append common images as files
                console.log('Common Images to append:', commonImages);
                commonImages.forEach((file, index) => {
                    let actualFile = file;
                    if (file && typeof file === 'object' && file.file instanceof File) {
                        actualFile = file.file;
                    }
                    
                    if (actualFile instanceof File || actualFile instanceof Blob) {
                        formData.append(`common_images[]`, actualFile);
                        console.log(`Appended common_images[${index}] = ${actualFile.name}`);
                    }
                });
                
                // Get form action and method
                const formAction = form.attr('action');
                const formMethod = form.attr('method') || 'POST';
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                console.log('=== AJAX Submission ===');
                console.log('URL:', formAction);
                console.log('Method:', formMethod);
                console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
                
                // Disable submit button and show loading state
                const $submitBtn = $('#submitBtn');
                const originalBtnHtml = $submitBtn.html();
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                // Submit form via AJAX
                $.ajax({
                    url: formAction,
                    type: formMethod,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('AJAX Success:', response);
                        $submitBtn.prop('disabled', false).html(originalBtnHtml);
                        
                        if (response.redirect) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message || 'Product saved successfully.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            window.location.href = '/admin/products';
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            error: error,
                            responseText: xhr.responseText,
                            responseJSON: xhr.responseJSON
                        });
                        
                        $submitBtn.prop('disabled', false).html(originalBtnHtml);
                        
                        let errorMsg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMsg = errors.join('<br>');
                        } else if (xhr.status === 0) {
                            errorMsg = 'Network error. Please check your connection and try again.';
                        } else if (xhr.status === 422) {
                            errorMsg = 'Validation error. Please check the form and try again.';
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            html: errorMsg,
                            icon: 'error',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                });
                
                return false;
                });
            }, 100); // Small delay to ensure wizard.js handler is attached first
        } else {
            // Fallback if Dropzone is not loaded
            console.warn('Dropzone.js is not loaded. Using fallback file input.');
            setupFallbackFileInputs();
        }
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImages);
    } else {
        initImages();
    }
})();

