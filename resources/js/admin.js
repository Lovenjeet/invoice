// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Import DataTables
import 'datatables.net';
import 'datatables.net-bs5';

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import Dropzone
import Dropzone from 'dropzone';
Dropzone.autoDiscover = false;
window.Dropzone = Dropzone;

// Admin Layout JS
$(document).ready(function() {
    // Sidebar toggle for mobile
    $('#mobileSidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('show');
        $('#sidebarOverlay').toggleClass('show');
    });
    
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').removeClass('show');
        $('#sidebarOverlay').removeClass('show');
    });
    
    $('#sidebarOverlay').on('click', function() {
        $('#sidebar').removeClass('show');
        $(this).removeClass('show');
    });
    
    // Set CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

