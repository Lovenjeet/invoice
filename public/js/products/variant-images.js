(function() {
    'use strict';
    
    // Wait for jQuery and dependencies to be available
    function initVariantImages() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initVariantImages, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        let currentVariantIndex = null;
        let currentVariantId = null;
        // Use window.variantImages directly to avoid reference issues
        if (!window.variantImages) {
            window.variantImages = {};
        }
        let variantImages = window.variantImages; // Use the same reference
        let variantDropzone = null;
        let isInitializingDropzone = false; // Flag to prevent multiple initializations
        
        // Helper to ensure window.variantImages is always in sync
        // Since we're using the same reference, this is mainly for logging
        function syncVariantImages() {
            // Since variantImages and window.variantImages are the same reference,
            // we just need to ensure it's accessible
            // Avoid infinite loops by checking if already synced
            if (window.variantImages !== variantImages) {
                window.variantImages = variantImages;
            }
            // Only log occasionally to avoid console spam
            if (Math.random() < 0.1) { // Log 10% of the time
                console.log('Synced - window.variantImages keys:', Object.keys(window.variantImages));
            }
        }
        
        // Initialize variant images storage
        function initVariantImagesStorage(index) {
            // Ensure index is a string for consistency
            const indexKey = String(index);
            if (!variantImages[indexKey]) {
                variantImages[indexKey] = [];
            }
            syncVariantImages();
        }
        
        // Open modal for variant image upload
        $(document).on('click', '.upload-variant-images', function() {
            const $row = $(this).closest('.variant-row');
            // Get index as string to match form field names
            currentVariantIndex = String($row.data('index') || $(this).data('index') || '0');
            currentVariantId = $row.data('variant-id') || $(this).data('variant-id') || '';
            const variantSize = $row.find('.variant-size').val() || 'New Variant';
            
            initVariantImagesStorage(currentVariantIndex);
            
            // Update modal title
            $('#modalVariantSize').text(variantSize || 'New Variant');
            
            // Clear preview
            $('#variantImagesPreview').empty();
            
            // Show already uploaded images for this variant index
            displayVariantImages(currentVariantIndex);
            
            // Load existing images if editing
            if (currentVariantId) {
                loadExistingVariantImages(currentVariantId);
            } else {
                // Update badge count for new variants (no existing images)
                setTimeout(function() {
                    updateVariantImagesBadge(currentVariantIndex);
                }, 100);
            }
            
            // Initialize or reset Dropzone
            if (variantDropzone) {
                variantDropzone.destroy();
                variantDropzone = null;
            }
            
            // Show modal using Bootstrap 5 native API
            const modalElement = document.getElementById('variantImagesModal');
            if (modalElement) {
                // Check if Bootstrap is available
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    // Get or create modal instance
                    let modal = bootstrap.Modal.getInstance(modalElement);
                    if (!modal) {
                        modal = new bootstrap.Modal(modalElement);
                    }
                    
                    // Use once option to prevent duplicate listeners
                    const handleModalShown = function() {
                        initVariantDropzone();
                        modalElement.removeEventListener('shown.bs.modal', handleModalShown);
                    };
                    
                    modalElement.removeEventListener('shown.bs.modal', handleModalShown);
                    modalElement.addEventListener('shown.bs.modal', handleModalShown, { once: true });
                    
                    modal.show();
                } else {
                    // Fallback: use data attributes
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    modalElement.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');
                    
                    // Remove existing backdrop if any
                    const existingBackdrop = document.getElementById('variantImagesModalBackdrop');
                    if (existingBackdrop) {
                        existingBackdrop.remove();
                    }
                    
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.setAttribute('id', 'variantImagesModalBackdrop');
                    document.body.appendChild(backdrop);
                    
                    // Initialize Dropzone after a short delay for fallback
                    setTimeout(function() {
                        initVariantDropzone();
                    }, 100);
                }
            }
        });
        
        // Preview variant image from file
        function previewVariantImageFromFile(file, variantIndex) {
            const fileId = 'file-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            file.fileId = fileId;
            
            // Check if preview already exists
            if ($(`#variantImagesPreview [data-file-id="${fileId}"]`).length > 0) {
                console.log('Preview already exists for file:', fileId);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="col-md-3 mb-3" data-file-id="${fileId}" data-file-name="${file.name}">
                        <div class="card position-relative">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate" title="${file.name}">${file.name}</small>
                                <div class="d-flex gap-1 mt-2">
                                    <button type="button" class="btn btn-sm btn-danger flex-fill remove-variant-image" data-file-id="${fileId}" data-file-name="${file.name}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#variantImagesPreview').append(previewHtml);
            };
            reader.readAsDataURL(file);
        }
        
        // Initialize Dropzone for variant images
        function initVariantDropzone() {
            // Prevent multiple simultaneous initializations
            if (isInitializingDropzone) {
                console.log('Dropzone initialization already in progress, skipping...');
                return;
            }
            
            // Check if Dropzone is already initialized and working
            if (variantDropzone && variantDropzone.element && document.body.contains(variantDropzone.element)) {
                console.log('Dropzone already initialized, skipping...');
                return;
            }
            
            const dropzoneElement = document.getElementById('variantImagesDropzone');
            if (!dropzoneElement) {
                console.warn('Dropzone element not found.');
                isInitializingDropzone = false;
                return;
            }
            
            isInitializingDropzone = true;
            
            // Wait a bit for Dropzone to load (in case it's loaded via Vite/module bundler)
            let dropzoneCheckAttempts = 0;
            const maxDropzoneCheckAttempts = 10;
            
            const checkDropzoneAndInit = function() {
                dropzoneCheckAttempts++;
                
                // Check for Dropzone in multiple ways (module bundler, CDN, etc.)
                const dropzoneAvailable = (
                    typeof Dropzone !== 'undefined' || 
                    typeof window.Dropzone !== 'undefined' ||
                    (typeof window !== 'undefined' && window.Dropzone)
                );
                
                if (!dropzoneAvailable && dropzoneCheckAttempts < maxDropzoneCheckAttempts) {
                    // Wait a bit more for Dropzone to load
                    setTimeout(checkDropzoneAndInit, 100);
                    return;
                }
                
                if (!dropzoneAvailable) {
                    console.warn('Dropzone.js is not loaded. Using fallback file input.');
                    // Show fallback file input with styling
                    dropzoneElement.innerHTML = `
                        <div class="border border-2 border-dashed rounded p-4 text-center" style="border-color: #0d6efd; background: #f8f9fa; cursor: pointer;" id="fileInputArea">
                            <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #0d6efd;"></i>
                            <p class="mt-2 mb-0">Click here or drag and drop images to upload</p>
                            <small class="text-muted">JPG, PNG, WEBP (Max 2MB each)</small>
                            <input type="file" id="variantImagesInput" name="variant_images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" class="d-none">
                        </div>
                    `;
                    
                    // Setup file input handler after a small delay to ensure DOM is ready
                    setTimeout(function() {
                        setupFileInputHandler();
                    }, 50);
                    isInitializingDropzone = false;
                    return;
                }
                
                // Dropzone is available, continue with initialization
                initDropzoneInstance();
            };
            
            // Start checking
            checkDropzoneAndInit();
            
            // Function to initialize Dropzone instance
            function initDropzoneInstance() {
                // Destroy existing instance FIRST, before any DOM manipulation
                if (variantDropzone) {
                    try {
                        // Remove event listeners before destroying
                        variantDropzone.off('addedfile');
                        variantDropzone.off('removedfile');
                        variantDropzone.destroy();
                    } catch (e) {
                        console.warn('Error destroying existing Dropzone:', e);
                    }
                    variantDropzone = null;
                }
                
                // Ensure element is visible
                dropzoneElement.style.display = 'block';
                dropzoneElement.style.minHeight = '200px';
                dropzoneElement.style.cursor = 'pointer';
                
                // Create a fresh element to avoid any DOM issues
                const parentElement = dropzoneElement.parentNode;
                const newDropzoneElement = document.createElement('div');
                newDropzoneElement.id = 'variantImagesDropzone';
                newDropzoneElement.className = 'dropzone mb-3';
                newDropzoneElement.style.cursor = 'pointer';
                parentElement.replaceChild(newDropzoneElement, dropzoneElement);
                
                // Small delay to ensure DOM is ready
                setTimeout(function() {
                    try {
                        // Define handlers outside init to prevent closure issues
                        const handleAddedFile = function(file) {
                            if (file.processed) {
                                return;
                            }
                            file.processed = true;
                            
                            const indexKey = String(currentVariantIndex);
                            if (!variantImages[indexKey]) {
                                variantImages[indexKey] = [];
                            }
                            
                            const fileToStore = file.file || file;
                            if (fileToStore instanceof File) {
                                // Store file object for upload on form submit
                                variantImages[indexKey].push(fileToStore);
                                window.variantImages = variantImages;
                                
                                // Show preview
                                previewVariantImageFromFile(fileToStore, currentVariantIndex);
                            }
                        };
                        
                        const handleRemovedFile = function(file) {
                            const indexKey = String(currentVariantIndex);
                            if (variantImages[indexKey]) {
                                const fileToRemove = file.file || file;
                                variantImages[indexKey] = variantImages[indexKey].filter(f => {
                                    return f !== fileToRemove && f.name !== fileToRemove.name;
                                });
                            }
                            window.variantImages = variantImages;
                            const fileId = file.fileId || 'file-' + (file.name || 'unknown');
                            removeVariantPreview(fileId, file.name || 'unknown', currentVariantIndex);
                        };
                        
                        // Get the new element reference after replacement
                        const finalDropzoneElement = document.getElementById('variantImagesDropzone');
                        if (!finalDropzoneElement) {
                            console.error('Dropzone element not found after replacement');
                            isInitializingDropzone = false;
                            return;
                        }
                        
                        // Ensure the element is clickable
                        finalDropzoneElement.style.cursor = 'pointer';
                        
                        // Use window.Dropzone if available, otherwise fallback to Dropzone
                        const DropzoneClass = window.Dropzone || Dropzone;
                        
                        variantDropzone = new DropzoneClass(finalDropzoneElement, {
                            url: '#',
                            autoProcessQueue: false,
                            addRemoveLinks: true,
                            maxFilesize: 2,
                            acceptedFiles: 'image/jpeg,image/jpg,image/png,image/webp',
                            dictDefaultMessage: '<i class="bi bi-cloud-upload" style="font-size: 2rem;"></i><br>Drag & drop images here or click to upload',
                            dictRemoveFile: 'Remove',
                            dictInvalidFileType: 'Invalid file type. Only JPG, PNG, and WEBP are allowed.',
                            dictFileTooBig: 'File is too big. Maximum size is 2MB.',
                            clickable: true, // Enable Dropzone's built-in click functionality
                            preventMultipleFiles: false,
                            init: function() {
                                const dzInstance = this;
                                
                                console.log('Dropzone initialized, element:', dzInstance.element);
                                console.log('Dropzone clickable:', dzInstance.clickable);
                                
                                // Attach handlers AFTER init
                                dzInstance.on('addedfile', handleAddedFile);
                                dzInstance.on('removedfile', handleRemovedFile);
                                
                                // Create a reliable click handler function
                                const handleClick = function(e) {
                                    console.log('=== CLICK HANDLER FIRED ===', e.target);
                                    
                                    // Don't trigger if clicking on remove buttons, previews, or other interactive elements
                                    const target = e.target;
                                    if (target.closest('.dz-remove') || 
                                        target.closest('.dz-preview') ||
                                        target.closest('button') ||
                                        target.closest('a') ||
                                        target.tagName === 'BUTTON' ||
                                        target.tagName === 'A' ||
                                        target.classList.contains('dz-remove')) {
                                        console.log('Click ignored - interactive element');
                                        return;
                                    }
                                    
                                    e.preventDefault();
                                    e.stopPropagation();
                                    
                                    console.log('Opening file picker...');
                                    
                                    // Create our own file input (most reliable method)
                                    const input = document.createElement('input');
                                    input.type = 'file';
                                    input.multiple = true;
                                    input.accept = 'image/jpeg,image/jpg,image/png,image/webp';
                                    input.style.display = 'none';
                                    input.style.position = 'absolute';
                                    input.style.left = '-9999px';
                                    
                                    input.addEventListener('change', function(changeEvent) {
                                        console.log('File selected:', changeEvent.target.files);
                                        const files = changeEvent.target.files;
                                        if (files && files.length > 0) {
                                            Array.from(files).forEach(function(file) {
                                                console.log('Processing file:', file.name, file.type, file.size);
                                                
                                                // Validate file type
                                                if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/i)) {
                                                    console.warn('Invalid file type:', file.type);
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            title: 'Invalid File',
                                                            text: 'Please select image files only (JPG, PNG, WEBP).',
                                                            icon: 'warning',
                                                            confirmButtonColor: '#0d6efd'
                                                        });
                                                    }
                                                    return;
                                                }
                                                
                                                // Validate file size (2MB)
                                                if (file.size > 2 * 1024 * 1024) {
                                                    console.warn('File too large:', file.size);
                                                    if (typeof Swal !== 'undefined') {
                                                        Swal.fire({
                                                            title: 'File Too Large',
                                                            text: `${file.name} is larger than 2MB. Please select a smaller file.`,
                                                            icon: 'warning',
                                                            confirmButtonColor: '#0d6efd'
                                                        });
                                                    }
                                                    return;
                                                }
                                                
                                                // Add file to Dropzone
                                                try {
                                                    console.log('Adding file to Dropzone:', file.name);
                                                    dzInstance.addFile(file);
                                                } catch (err) {
                                                    console.error('Error adding file to Dropzone:', err);
                                                }
                                            });
                                        }
                                        
                                        // Clean up
                                        setTimeout(function() {
                                            if (input.parentNode) {
                                                input.parentNode.removeChild(input);
                                            }
                                        }, 100);
                                    });
                                    
                                    document.body.appendChild(input);
                                    console.log('Triggering file input click...');
                                    input.click();
                                };
                                
                                // Ensure clickable is enabled and working
                                if (dzInstance.element) {
                                    console.log('Setting up click handlers on element:', dzInstance.element);
                                    console.log('Element classes:', dzInstance.element.className);
                                    console.log('Element style:', dzInstance.element.style.cssText);
                                    
                                    dzInstance.element.style.cursor = 'pointer';
                                    dzInstance.element.style.pointerEvents = 'auto';
                                    
                                    // Attach click handler immediately with capture phase (fires first)
                                    dzInstance.element.addEventListener('click', handleClick, true);
                                    console.log('Click handler attached to main element');
                                    
                                    // Also ensure the message area is clickable
                                    setTimeout(function() {
                                        console.log('Setting up delayed click handlers...');
                                        
                                        // Try multiple selectors to find clickable areas
                                        const selectors = ['.dz-message', '.dz-default', '.dz-clickable'];
                                        selectors.forEach(function(selector) {
                                            const elements = dzInstance.element.querySelectorAll(selector);
                                            elements.forEach(function(el) {
                                                console.log('Found element with selector', selector, el);
                                                el.style.cursor = 'pointer';
                                                el.style.pointerEvents = 'auto';
                                                el.addEventListener('click', handleClick, true);
                                            });
                                        });
                                        
                                        // Also attach directly to the element itself
                                        dzInstance.element.addEventListener('click', handleClick, true);
                                        
                                        // Make the entire element clickable
                                        const allChildren = dzInstance.element.querySelectorAll('*');
                                        allChildren.forEach(function(child) {
                                            // Skip interactive elements
                                            if (child.tagName !== 'BUTTON' && 
                                                child.tagName !== 'A' && 
                                                !child.classList.contains('dz-remove') &&
                                                !child.closest('.dz-preview')) {
                                                child.style.pointerEvents = 'auto';
                                            }
                                        });
                                        
                                        // Check if Dropzone created a hidden file input
                                        if (dzInstance.hiddenFileInput) {
                                            console.log('Dropzone hiddenFileInput found:', dzInstance.hiddenFileInput);
                                        } else {
                                            console.warn('Dropzone hiddenFileInput not found, using fallback');
                                        }
                                        
                                        console.log('All click handlers set up. Try clicking the dropzone now.');
                                    }, 300);
                                } else {
                                    console.error('dzInstance.element is null!');
                                }
                                
                                isInitializingDropzone = false;
                            }
                        });
                    } catch (e) {
                        console.error('Error initializing Dropzone:', e);
                        console.error('Stack:', e.stack);
                        isInitializingDropzone = false;
                    }
                }, 100);
            }
        }
        
        // Setup file input handler for fallback
        function setupFileInputHandler() {
            // Ensure jQuery is available
            const $jq = window.$ || window.jQuery;
            if (typeof $jq === 'undefined') {
                console.error('jQuery is required for fallback file input handler');
                return;
            }
            
            // Wait for elements to be in DOM
            const checkAndSetup = function(attempts) {
                attempts = attempts || 0;
                const $fileInputArea = $jq('#fileInputArea');
                const $fileInput = $jq('#variantImagesInput');
                
                if ((!$fileInputArea.length || !$fileInput.length) && attempts < 10) {
                    setTimeout(function() {
                        checkAndSetup(attempts + 1);
                    }, 50);
                    return;
                }
                
                if (!$fileInputArea.length || !$fileInput.length) {
                    console.error('File input elements not found');
                    return;
                }
                
                // Remove existing handlers to avoid duplicates
                $fileInputArea.off('click');
                $fileInput.off('change');
                $fileInputArea.off('dragover dragleave drop');
                
                // Click on area to trigger file input
                $fileInputArea.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Only trigger if file input exists and is not already being triggered
                    if ($fileInput.length && !$fileInput.data('triggering')) {
                        $fileInput.data('triggering', true);
                        try {
                            $fileInput[0].click();
                        } catch (err) {
                            console.error('Error triggering file input:', err);
                        } finally {
                            setTimeout(function() {
                                $fileInput.data('triggering', false);
                            }, 100);
                        }
                    }
                });
                
                // Handle file selection
                $fileInput.on('change', function(e) {
                    const files = e.target.files;
                    if (files.length > 0) {
                        Array.from(files).forEach(function(file) {
                            if (file.type.match('image.*')) {
                                // Check file size (2MB limit)
                                if (file.size > 2 * 1024 * 1024) {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            title: 'File Too Large',
                                            text: `${file.name} is larger than 2MB. Please select a smaller file.`,
                                            icon: 'warning',
                                            confirmButtonColor: '#0d6efd'
                                        });
                                    } else {
                                        alert(`${file.name} is larger than 2MB. Please select a smaller file.`);
                                    }
                                    return;
                                }
                                
                                const indexKey = String(currentVariantIndex);
                                if (!variantImages[indexKey]) {
                                    variantImages[indexKey] = [];
                                }
                                
                                // Store file for upload on form submit
                                variantImages[indexKey].push(file);
                                syncVariantImages();
                                console.log(`File Input: Added file ${file.name} to variant ${indexKey}`);
                                
                                // Show preview
                                previewVariantImageFromFile(file, currentVariantIndex);
                            } else {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'Invalid File',
                                        text: 'Please select image files only.',
                                        icon: 'warning',
                                        confirmButtonColor: '#0d6efd'
                                    });
                                } else {
                                    alert('Please select image files only.');
                                }
                            }
                        });
                        // Reset input to allow selecting same file again
                        $fileInput.val('');
                    }
                });
                
                // Drag and drop support
                $fileInputArea.on('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $jq(this).css('background-color', '#e7f1ff');
                });
                
                $fileInputArea.on('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $jq(this).css('background-color', '#f8f9fa');
                });
                
                $fileInputArea.on('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $jq(this).css('background-color', '#f8f9fa');
                    
                    const files = e.originalEvent.dataTransfer.files;
                    if (files.length > 0) {
                        Array.from(files).forEach(function(file) {
                            if (file.type.match('image.*')) {
                                // Check file size (2MB limit)
                                if (file.size > 2 * 1024 * 1024) {
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            title: 'File Too Large',
                                            text: `${file.name} is larger than 2MB. Please select a smaller file.`,
                                            icon: 'warning',
                                            confirmButtonColor: '#0d6efd'
                                        });
                                    }
                                    return;
                                }
                                
                                const indexKey = String(currentVariantIndex);
                                if (!variantImages[indexKey]) {
                                    variantImages[indexKey] = [];
                                }
                                
                                // Store file for upload on form submit
                                variantImages[indexKey].push(file);
                                syncVariantImages();
                                console.log(`Drag & Drop: Added file ${file.name} to variant ${indexKey}`);
                                
                                // Show preview
                                previewVariantImageFromFile(file, currentVariantIndex);
                            }
                        });
                    }
                });
            };
            
            checkAndSetup();
        }
        
        // Preview variant image
        function previewVariantImage(file, variantIndex) {
            // Get the actual File object for reading
            const fileToRead = file.file || file;
            
            // Check if file is valid
            if (!fileToRead || !(fileToRead instanceof File || fileToRead instanceof Blob)) {
                console.error('Cannot preview: file is not a File or Blob:', fileToRead);
                return;
            }
            
            // Prevent duplicate previews
            const fileId = file.fileId || fileToRead.fileId || 'file-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            if (!file.fileId) {
                file.fileId = fileId;
            }
            if (!fileToRead.fileId) {
                fileToRead.fileId = fileId;
            }
            
            // Check if preview already exists
            if ($(`#variantImagesPreview [data-file-id="${fileId}"]`).length > 0) {
                console.log('Preview already exists for file:', fileId);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewHtml = `
                    <div class="col-md-3 mb-3" data-file-id="${fileId}" data-file-name="${fileToRead.name || file.name}">
                        <div class="card position-relative">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate" title="${fileToRead.name || file.name}">${fileToRead.name || file.name}</small>
                                <small class="text-muted d-block">${((fileToRead.size || file.size) / 1024).toFixed(2)} KB</small>
                                <div class="d-flex gap-1 mt-2">
                                    <button type="button" class="btn btn-sm btn-danger flex-fill remove-variant-image" data-file-id="${fileId}" data-file-name="${fileToRead.name || file.name}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#variantImagesPreview').append(previewHtml);
                
                // Update badge count after adding preview
                updateVariantImagesBadge(variantIndex);
            };
            reader.onerror = function(e) {
                console.error('Error reading file:', e);
            };
            reader.readAsDataURL(fileToRead);
        }
        
        // Remove variant preview
        function removeVariantPreview(fileId, fileName, variantIndex) {
            const indexKey = String(variantIndex);
            
            // Remove from preview
            const $preview = $(`#variantImagesPreview [data-file-id="${fileId}"]`);
            
            $preview.fadeOut(300, function() {
                $(this).remove();
                
                // Update badge count after removal
                updateVariantImagesBadge(variantIndex);
            });
            
            // Remove from variantImages array
            if (variantImages[indexKey]) {
                variantImages[indexKey] = variantImages[indexKey].filter(f => {
                    return f.fileId !== fileId && f.name !== fileName;
                });
            }
            syncVariantImages();
            
            // Remove from Dropzone if it exists
            if (variantDropzone) {
                const file = variantDropzone.files.find(f => {
                    return f.name === fileName || f.fileId === fileId;
                });
                if (file) {
                    variantDropzone.removeFile(file);
                }
            }
        }
        
        // Remove variant image button handler
        $(document).on('click', '.remove-variant-image', function() {
            const fileId = $(this).data('file-id');
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
                        removeVariantPreview(fileId, fileName, currentVariantIndex);
                    }
                });
            } else {
                if (confirm('Are you sure you want to remove this image?')) {
                    removeVariantPreview(fileId, fileName, currentVariantIndex);
                }
            }
        });
        
        // Load existing images for editing
        function loadExistingVariantImages(variantId) {
            $.ajax({
                url: `/admin/variants/${variantId}/images`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.images && response.images.length > 0) {
                        const indexKey = String(currentVariantIndex);
                        
                        // Initialize array if needed
                        if (!variantImages[indexKey]) {
                            variantImages[indexKey] = [];
                        }
                        
                        response.images.forEach(function(image) {
                            // Store existing image info (for display only, not for upload)
                            const imageHtml = `
                                <div class="col-md-3 mb-3" data-existing-image-id="${image.id}" data-file-id="existing-${image.id}" data-file-name="${image.file_name || 'image-' + image.id}">
                                    <div class="card">
                                        <img src="${image.url}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="Product Image">
                                        <div class="card-body p-2">
                                            <button type="button" class="btn btn-sm btn-danger mt-2 remove-existing-variant-image" data-image-id="${image.id}" data-file-id="existing-${image.id}" data-file-name="${image.file_name || 'image-' + image.id}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#variantImagesPreview').append(imageHtml);
                        });
                        
                        syncVariantImages();
                        
                        // Update badge count after loading existing images
                        updateVariantImagesBadge(currentVariantIndex);
                    }
                },
                error: function() {
                    console.error('Failed to load existing images');
                }
            });
        }
        
        // Update badge count for variant images button
        function updateVariantImagesBadge(variantIndex) {
            const indexKey = String(variantIndex);
            const files = variantImages[indexKey] || [];
            const existingImagesCount = $('#variantImagesPreview [data-existing-image-id]').length;
            const newFilesCount = files.length;
            const totalCount = existingImagesCount + newFilesCount;
            
            const $uploadBtn = $(`.upload-variant-images[data-index="${variantIndex}"]`);
            let badgeHtml = '';
            if (totalCount > 0) {
                badgeHtml = ` <span class="badge bg-light text-dark ms-1">${totalCount}</span>`;
            }
            $uploadBtn.html(`<i class="bi bi-images"></i>${badgeHtml}`);
        }
        
        // Display variant images for current variant index
        function displayVariantImages(variantIndex) {
            const indexKey = String(variantIndex);
            const images = variantImages[indexKey] || [];
            images.forEach(function(file) {
                previewVariantImage(file, variantIndex);
            });
        }
        
        // Save variant images
        $('#saveVariantImages').on('click', function() {
            const indexKey = String(currentVariantIndex);
            console.log(indexKey);
            
            
            // Get files from variantImages object (new files to be uploaded)
            const files = variantImages[indexKey] || [];
            
            // Count existing images (already uploaded, displayed in preview)
            const existingImagesCount = $('#variantImagesPreview [data-existing-image-id]').length;
            
            // Count new files (to be uploaded on form submit)
            const newFilesCount = files.length;
            
            // Total count = existing images + new files
            const totalCount = existingImagesCount + newFilesCount;
            
            console.log('=== Save Variant Images ===');
            console.log('Variant index:', indexKey);
            console.log('Existing images count:', existingImagesCount);
            console.log('New files count:', newFilesCount);
            console.log('Total count:', totalCount);
            
            // Update badge count on button
            updateVariantImagesBadge(currentVariantIndex);
            
            // Close modal using Bootstrap 5 native API
            const modalElement = document.getElementById('variantImagesModal');
            if (modalElement) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    // Fallback: manual hide
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                    modalElement.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.getElementById('variantImagesModalBackdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Images Saved!',
                    text: `${totalCount} image(s) total for this variant (${existingImagesCount} existing + ${newFilesCount} new).`,
                    icon: 'success',
                    confirmButtonColor: '#0d6efd',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
        
        // Handle removal of existing images (for edit mode)
        $(document).on('click', '.remove-existing-variant-image', function() {
            const imageId = $(this).data('image-id');
            const $imageCard = $(this).closest('[data-existing-image-id]');
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This image will be deleted permanently.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteVariantImage(imageId, $imageCard);
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete this image?')) {
                    deleteVariantImage(imageId, $imageCard);
                }
            }
        });
        
        function deleteVariantImage(imageId, $imageCard) {
            $.ajax({
                url: `/admin/product-images/${imageId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $imageCard.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update badge count after deletion
                        updateVariantImagesBadge(currentVariantIndex);
                    });
                },
                error: function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to delete image.',
                            icon: 'error',
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                }
            });
        }
        
        // Clean up Dropzone when modal is closed
        const modalElement = document.getElementById('variantImagesModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                // Reset initialization flag when modal is closed
                isInitializingDropzone = false;
                // Keep Dropzone instance but clear files that weren't saved
                // The instance will be destroyed when modal opens again
            });
        }
        
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initVariantImages);
    } else {
        initVariantImages();
    }
})();

