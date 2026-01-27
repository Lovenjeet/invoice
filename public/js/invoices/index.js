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
            const invoicesTable = $('#invoicesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/invoices',
                    type: 'GET',
                    data: function(d) {
                        // Add filter parameters
                        if ($('#statusFilter').length) {
                            d.status_filter = $('#statusFilter').val();
                        }
                        if ($('#supplierFilter').length) {
                            d.supplier_filter = $('#supplierFilter').val();
                        }
                        if ($('#billToFilter').length) {
                            d.bill_to_filter = $('#billToFilter').val();
                        }
                        if ($('#shipToFilter').length) {
                            d.ship_to_filter = $('#shipToFilter').val();
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load invoices. Please refresh the page.';
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
                    { data: 'unc_number', name: 'unc_number' },
                    { data: 'approval_email', name: 'approval_email' },
                    { data: 'supplier', name: 'supplier', orderable: false },
                    { data: 'bill_to', name: 'bill_to', orderable: false },
                    { data: 'ship_to', name: 'ship_to', orderable: false },
                    { data: 'total', name: 'total' },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search invoices...",
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
                    emptyTable: "No invoices found",
                    zeroRecords: "No matching invoices found"
                },
                columnDefs: [
                    { orderable: false, targets: [2, 3, 4, 6, 7] }
                ],
                drawCallback: function() {
                    initDropdowns();
                }
            });
            
            function initDropdowns() {
                // Initialize Bootstrap dropdowns after table redraw
                if (typeof bootstrap !== 'undefined') {
                    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                    dropdownElementList.forEach(function(dropdownToggleEl) {
                        new bootstrap.Dropdown(dropdownToggleEl);
                    });
                }
            }
            
            initDropdowns();
            
            // Custom search input
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = $(this).val();
                searchTimeout = setTimeout(function() {
                    invoicesTable.search(searchValue).draw();
                }, 300);
            });
            
            // Filters
            $('#statusFilter, #supplierFilter, #billToFilter, #shipToFilter').on('change', function() {
                invoicesTable.ajax.reload();
            });
            
            // Clear filters
            $('#clearFilters').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                $('#supplierFilter').val('');
                $('#billToFilter').val('');
                $('#shipToFilter').val('');
                invoicesTable.search('').draw();
                invoicesTable.ajax.reload();
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

