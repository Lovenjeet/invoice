(function() {
    'use strict';
    
    function initRechargeOffers() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initRechargeOffers, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            const modal = new bootstrap.Modal(document.getElementById('rechargeOfferModal'));
            const form = $('#rechargeOfferForm');
            let isEditMode = false;
            
            // Create button handlers
            $('#createOfferBtn, #createOfferBtnEmpty').on('click', function() {
                resetForm();
                isEditMode = false;
                $('#rechargeOfferModalLabel').text('Create Recharge Offer');
                $('#submitBtn').html('<i class="bi bi-check-lg me-2"></i>Create Offer');
                form.attr('action', '/recharge-offers');
                form.find('input[name="_method"]').remove();
                modal.show();
            });
            
            // Edit button handlers
            $(document).on('click', '.edit-offer', function(e) {
                e.preventDefault();
                const offerId = $(this).data('offer-id');
                loadOfferData(offerId);
            });
            
            // Delete button handlers
            $(document).on('click', '.delete-offer', function(e) {
                e.preventDefault();
                const offerId = $(this).data('offer-id');
                const offerAmount = $(this).data('offer-amount') || 'this offer';
                
                if (typeof Swal === 'undefined') {
                    if (confirm(`Do you want to delete ${offerAmount}? This action cannot be undone.`)) {
                        deleteOffer(offerId);
                    }
                    return;
                }
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete ${offerAmount}? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteOffer(offerId);
                    }
                });
            });
            
            // Form submission
            form.on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
            
            // Load offer data for editing
            function loadOfferData(offerId) {
                $.ajax({
                    url: `/recharge-offers/${offerId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.offer) {
                            const offer = response.offer;
                            isEditMode = true;
                            
                            $('#offer_id').val(offer.id);
                            $('#min_recharge_amount').val(offer.min_recharge_amount);
                            $('#cashback_amount').val(offer.cashback_amount);
                            
                            // Format expiry date for date input (YYYY-MM-DD)
                            if (offer.expiry_date) {
                                const expiryDate = new Date(offer.expiry_date);
                                const formattedDate = expiryDate.toISOString().split('T')[0];
                                $('#expiry_date').val(formattedDate);
                            } else {
                                $('#expiry_date').val('');
                            }
                            
                            $('#is_active').prop('checked', offer.is_active);
                            
                            $('#rechargeOfferModalLabel').text('Edit Recharge Offer');
                            $('#submitBtn').html('<i class="bi bi-check-lg me-2"></i>Update Offer');
                            form.attr('action', `/recharge-offers/${offerId}`);
                            
                            // Add method spoofing for PUT
                            if (!form.find('input[name="_method"]').length) {
                                form.append('<input type="hidden" name="_method" value="PUT">');
                            }
                            
                            modal.show();
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to load offer data.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            alert('Error: ' + errorMsg);
                        }
                    }
                });
            }
            
            // Submit form
            function submitForm() {
                const formData = new FormData(form[0]);
                const url = form.attr('action');
                const method = form.find('input[name="_method"]').val() || 'POST';
                
                // Reset validation
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
                
                // Show loading
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                
                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            modal.hide();
                            
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#0d6efd'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                alert(response.message);
                                location.reload();
                            }
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            
                            // Display validation errors
                            Object.keys(errors).forEach(function(key) {
                                const field = form.find(`[name="${key}"]`);
                                field.addClass('is-invalid');
                                const feedback = field.siblings('.invalid-feedback');
                                if (feedback.length) {
                                    feedback.text(errors[key][0]);
                                } else {
                                    field.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                                }
                            });
                        } else {
                            const errorMsg = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMsg,
                                    icon: 'error',
                                    confirmButtonColor: '#0d6efd'
                                });
                            } else {
                                alert('Error: ' + errorMsg);
                            }
                        }
                    }
                });
            }
            
            // Delete offer
            function deleteOffer(offerId) {
                $.ajax({
                    url: `/recharge-offers/${offerId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Recharge offer has been deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#0d6efd'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                alert('Recharge offer deleted successfully.');
                                location.reload();
                            }
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            alert('Error: ' + errorMsg);
                        }
                    }
                });
            }
            
            // Reset form
            function resetForm() {
                form[0].reset();
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
                $('#offer_id').val('');
                $('#is_active').prop('checked', true);
            }
            
            // Initialize dropdowns
            function initDropdowns() {
                if (typeof bootstrap !== 'undefined') {
                    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                    dropdownElementList.forEach(function(dropdownToggleEl) {
                        new bootstrap.Dropdown(dropdownToggleEl);
                    });
                }
            }
            
            initDropdowns();
            
            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRechargeOffers);
    } else {
        initRechargeOffers();
    }
})();

