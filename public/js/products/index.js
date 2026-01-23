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
            const productsTable = $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/products',
                    type: 'GET',
                    data: function(d) {
                        // Add filter parameters
                        if ($('#categoryFilter').length) {
                            d.category_filter = $('#categoryFilter').val();
                        }
                        if ($('#statusFilter').length) {
                            d.status_filter = $('#statusFilter').val();
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load products. Please refresh the page.';
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
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'category', name: 'category', orderable: false },
                    { data: 'price', name: 'price', orderable: false },
                    { data: 'variants', name: 'variants', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search products...",
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
                    emptyTable: "No products found",
                    zeroRecords: "No matching products found"
                },
                columnDefs: [
                    { orderable: false, targets: [1, 3, 4, 5, 6] } // Image, Category, Price, Variants, Actions columns
                ],
                drawCallback: function() {
                    // Re-initialize delete handlers after table redraw
                    initDeleteHandlers();
                    // Re-initialize Bootstrap dropdowns
                    initDropdowns();
                }
            });
            
            // Delete product handler with SweetAlert2
            function initDeleteHandlers() {
                $('.delete-product').off('click').on('click', function() {
                    const productId = $(this).data('id');
                    const productName = $(this).data('name');
                    
                    if (typeof Swal === 'undefined') {
                        if (confirm(`Do you want to delete "${productName}"? This action cannot be undone.`)) {
                            deleteProduct(productId);
                        }
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to delete "${productName}"? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteProduct(productId);
                        }
                    });
                });
            }
            
            function deleteProduct(productId) {
                $.ajax({
                    url: `/products/${productId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Product has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                productsTable.ajax.reload(null, false);
                            });
                        } else {
                            alert('Product deleted successfully.');
                            productsTable.ajax.reload(null, false);
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
            
            // Initialize Bootstrap dropdowns
            function initDropdowns() {
                // Bootstrap dropdowns are auto-initialized, but we ensure they work after DataTables redraw
                const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
                dropdownElementList.forEach(function(dropdownToggleEl) {
                    // Bootstrap 5 dropdowns auto-initialize, but we can ensure they're properly set up
                    if (typeof bootstrap !== 'undefined') {
                        new bootstrap.Dropdown(dropdownToggleEl);
                    }
                });
            }
            
            // Initialize delete handlers on page load
            initDeleteHandlers();
            initDropdowns();
            
            // Custom search input - sync with DataTables search
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = $(this).val();
                searchTimeout = setTimeout(function() {
                    productsTable.search(searchValue).draw();
                }, 300);
            });
            
            // Category filter
            $('#categoryFilter').on('change', function() {
                productsTable.ajax.reload();
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
