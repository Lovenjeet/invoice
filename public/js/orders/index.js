(function() {
    'use strict';
    
    // Wait for jQuery to be available
    function initIndex() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initIndex, 100);
            return;
        }
        
        const $ = window.jQuery || jQuery;
        
        $(document).ready(function() {

            // Initialize DataTable with AJAX
            const ordersTable = $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/orders',
                    type: 'GET',
                    data: function(d) {
                        // Add filter parameters
                        if ($('#searchInput').length) {
                            d.search = $('#searchInput').val();
                        }
                        if ($('#deliveryDateFilter').length && $('#deliveryDateFilter').val()) {
                            d.delivery_date = $('#deliveryDateFilter').val();
                        }
                        if ($('#statusFilter').length && $('#statusFilter').val()) {
                            d.status_filter = $('#statusFilter').val();
                        }
                        if ($('#orderTypeFilter').length && $('#orderTypeFilter').val()) {
                            d.order_type_filter = $('#orderTypeFilter').val();
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load orders. Please refresh the page.';
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
                    { data: 'order_number', name: 'order_number' },
                    { data: 'total_amount', name: 'total_amount', orderable: false },
                    { data: 'items_count', name: 'items_count', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'order_type', name: 'order_type', orderable: false },
                    { data: 'delivery_date', name: 'delivery_date' },
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
                    searchPlaceholder: "Search orders...",
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
                    emptyTable: "No orders found",
                    zeroRecords: "No matching orders found"
                },
                columnDefs: [
                    { orderable: false, targets: [2, 3, 4, 5, 8] } // Total Amount, Items, Status, Order Type, Actions columns
                ],
                drawCallback: function() {
                    // Re-initialize Bootstrap dropdowns
                    initDropdowns();
                }
            });
            
            // Initialize Bootstrap dropdowns
            function initDropdowns() {
                const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                dropdownElementList.forEach(function(dropdownToggleEl) {
                    if (typeof bootstrap !== 'undefined') {
                        new bootstrap.Dropdown(dropdownToggleEl);
                    }
                });
            }
            
            // Initialize dropdowns on page load
            initDropdowns();
            
            // Custom search input - sync with DataTables search
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = $(this).val();
                searchTimeout = setTimeout(function() {
                    ordersTable.search(searchValue).draw();
                }, 300);
            });
            
            // Delivery Date filter
            $('#deliveryDateFilter').on('change', function() {
                ordersTable.ajax.reload();
            });
            
            // Status filter
            $('#statusFilter').on('change', function() {
                ordersTable.ajax.reload();
            });
            
            // Order Type filter
            $('#orderTypeFilter').on('change', function() {
                ordersTable.ajax.reload();
            });
            
            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#deliveryDateFilter').val('');
                $('#statusFilter').val('');
                $('#orderTypeFilter').val('');
                ordersTable.search('').draw();
                ordersTable.ajax.reload();
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIndex);
    } else {
        initIndex();
    }
})();

