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
            const usersTable = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/users',
                    type: 'GET',
                    data: function(d) {
                        // Add filter parameters
                        if ($('#roleFilter').length) {
                            d.role_filter = $('#roleFilter').val();
                        }
                        return d;
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        const errorMsg = 'Failed to load users. Please refresh the page.';
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
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'role', name: 'role', orderable: false },
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
                    searchPlaceholder: "Search users...",
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
                    emptyTable: "No users found",
                    zeroRecords: "No matching users found"
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
                $('.delete-user').off('click').on('click', function(e) {
                    e.preventDefault();
                    const userId = $(this).data('id');
                    const userName = $(this).data('name');
                    
                    if (typeof Swal === 'undefined') {
                        if (confirm(`Do you want to delete "${userName}"? This action cannot be undone.`)) {
                            deleteUser(userId);
                        }
                        return;
                    }
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to delete "${userName}"? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteUser(userId);
                        }
                    });
                });
            }
            
            function deleteUser(userId) {
                $.ajax({
                    url: `/users/${userId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'User has been deleted.',
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            }).then(() => {
                                usersTable.ajax.reload(null, false);
                            });
                        } else {
                            alert('User deleted successfully.');
                            usersTable.ajax.reload(null, false);
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
            
            // Custom search input with debounce
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchValue = $(this).val();
                const $input = $(this);
                
                // Add loading indicator
                if (searchValue.length > 0) {
                    $input.css('background-image', 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236c757d\' stroke-width=\'2\'%3E%3Cpath d=\'M21 12a9 9 0 1 1-6.219-8.56\'/%3E%3C/svg%3E")');
                    $input.css('background-repeat', 'no-repeat');
                    $input.css('background-position', 'right 0.75rem center');
                    $input.css('padding-right', '2.5rem');
                } else {
                    $input.css('background-image', '');
                    $input.css('padding-right', '');
                }
                
                searchTimeout = setTimeout(function() {
                    usersTable.search(searchValue).draw();
                    $input.css('background-image', '');
                    $input.css('padding-right', '');
                }, 300);
            });
            
            // Role filter with smooth transition
            $('#roleFilter').on('change', function() {
                const $select = $(this);
                $select.prop('disabled', true);
                usersTable.ajax.reload(function() {
                    $select.prop('disabled', false);
                });
            });
            
            // Clear filters with animation
            $('#clearFilters').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                
                $btn.prop('disabled', true);
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Clearing...');
                
                $('#searchInput').val('');
                $('#roleFilter').val('');
                usersTable.search('').draw();
                usersTable.ajax.reload(function() {
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                });
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

