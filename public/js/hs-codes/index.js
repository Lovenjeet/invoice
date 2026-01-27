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
            const hsCodesTable = $('#hsCodesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/hs-codes',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load SKUs. Please refresh the page.';
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
                    { data: 'model', name: 'model' },
                    { data: 'sku', name: 'sku' },
                    { data: 'hs_code', name: 'hs_code' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Search SKUs...",
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
                    emptyTable: "No SKUs found",
                    zeroRecords: "No matching SKUs found"
                },
                columnDefs: [
                    { orderable: false, targets: [3] }
                ],
                drawCallback: function() {
                    initDeleteHandlers();
                    initDropdowns();
                }
            });
            
            function initDeleteHandlers() {
                $('.delete-hs-code').off('click').on('click', function(e) {
                    e.preventDefault();
                    const hsCodeId = $(this).data('id');
                    const hsCodeName = $(this).data('name');
                    
                    if (typeof Swal === 'undefined') {
                        if (confirm(`Do you want to delete "${hsCodeName}"? This action cannot be undone.`)) {
                            deleteHSCode(hsCodeId);
                        }
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to delete "${hsCodeName}"? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteHSCode(hsCodeId);
                        }
                    });
                });
            }
            
            function deleteHSCode(hsCodeId) {
                $.ajax({
                    url: `/hs-codes/${hsCodeId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'SKU has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                hsCodesTable.ajax.reload(null, false);
                            });
                        } else {
                            alert('SKU deleted successfully.');
                            hsCodesTable.ajax.reload(null, false);
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
                    hsCodesTable.search(searchValue).draw();
                }, 300);
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

