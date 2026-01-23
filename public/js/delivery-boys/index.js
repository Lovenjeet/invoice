(function() {
    'use strict';
    
    function initIndex() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initIndex, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {
            // Initialize DataTable with AJAX
            const deliveryBoysTable = $('#deliveryBoysTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/delivery-boys',
                    type: 'GET',
                    data: function(d) {
                        // Add filter parameters
                        if ($('#employeeTypeFilter').length) {
                            d.employee_type_filter = $('#employeeTypeFilter').val();
                        }
                        if ($('#vehicleTypeFilter').length) {
                            d.vehicle_type_filter = $('#vehicleTypeFilter').val();
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load delivery boys. Please refresh the page.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error!',
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            alert(errorMsg);
                        }
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'city', name: 'city' },
                    { data: 'employee_type', name: 'employee_type', orderable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search delivery boys...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No delivery boys found",
                    zeroRecords: "No matching delivery boys found"
                },
                columnDefs: [
                    { orderable: false, targets: [4, 6] }
                ],
                drawCallback: function() {
                    initDeleteHandlers();
                    initDropdowns();
                }
            });
            
            function initDeleteHandlers() {
                $('.delete-delivery-boy').off('click').on('click', function(e) {
                    e.preventDefault();
                    const deliveryBoyId = $(this).data('id');
                    const deliveryBoyName = $(this).data('name');
                    
                    if (typeof Swal === 'undefined') {
                        if (confirm(`Do you want to delete "${deliveryBoyName}"? This action cannot be undone.`)) {
                            deleteDeliveryBoy(deliveryBoyId);
                        }
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to delete "${deliveryBoyName}"? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteDeliveryBoy(deliveryBoyId);
                        }
                    });
                });
            }
            
            function deleteDeliveryBoy(deliveryBoyId) {
                $.ajax({
                    url: `/delivery-boys/${deliveryBoyId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Delivery boy has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                deliveryBoysTable.ajax.reload(null, false);
                            });
                        } else {
                            alert('Delivery boy deleted successfully.');
                            deliveryBoysTable.ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        } else {
                            alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong. Please try again.'));
                        }
                    }
                });
            }
            
            function initDropdowns() {
                // Initialize Bootstrap dropdowns after table redraw
                if (typeof bootstrap !== 'undefined') {
                    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                    dropdownElementList.forEach(function(dropdownToggleEl) {
                        new bootstrap.Dropdown(dropdownToggleEl);
                    });
                }
            }
            
            initDeleteHandlers();
            initDropdowns();
            
            // Custom search input
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = $(this).val();
                searchTimeout = setTimeout(function() {
                    deliveryBoysTable.search(searchValue).draw();
                }, 300);
            });
            
            // Employee type filter
            $('#employeeTypeFilter').on('change', function() {
                deliveryBoysTable.ajax.reload();
            });
            
            // Vehicle type filter
            $('#vehicleTypeFilter').on('change', function() {
                deliveryBoysTable.ajax.reload();
            });
            
            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#employeeTypeFilter').val('');
                $('#vehicleTypeFilter').val('');
                deliveryBoysTable.search('').draw();
                deliveryBoysTable.ajax.reload();
            });
            
            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIndex);
    } else {
        initIndex();
    }
})();

