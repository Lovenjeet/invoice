/**
 * Idle Detection and Auto-Logout
 * Automatically logs out user after 1 hour of inactivity
 */
(function() {
    'use strict';
    
    // Configuration
    const IDLE_TIMEOUT = 60 * 60 * 1000; // 1 hour in milliseconds
    const WARNING_TIME = 5 * 60 * 1000; // Show warning 5 minutes before logout
    const CHECK_INTERVAL = 60 * 1000; // Check every minute
    
    let idleTimer = null;
    let warningTimer = null;
    let lastActivity = Date.now();
    let warningShown = false;
    
    // Events that indicate user activity
    const activityEvents = [
        'mousedown',
        'mousemove',
        'keypress',
        'scroll',
        'touchstart',
        'click'
    ];
    
    /**
     * Reset the idle timer
     */
    function resetIdleTimer() {
        lastActivity = Date.now();
        warningShown = false;
        
        // Clear existing timers
        if (idleTimer) {
            clearTimeout(idleTimer);
        }
        if (warningTimer) {
            clearTimeout(warningTimer);
        }
        
        // Set warning timer (5 minutes before logout)
        warningTimer = setTimeout(function() {
            showWarning();
        }, IDLE_TIMEOUT - WARNING_TIME);
        
        // Set logout timer
        idleTimer = setTimeout(function() {
            performLogout();
        }, IDLE_TIMEOUT);
    }
    
    /**
     * Show warning message before logout
     */
    function showWarning() {
        if (warningShown) return;
        warningShown = true;
        
        // Create warning modal/dialog
        const warningHtml = `
            <div id="idle-warning-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    max-width: 400px;
                    text-align: center;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                ">
                    <h5 style="margin-bottom: 15px; color: #dc3545;">Session Timeout Warning</h5>
                    <p style="margin-bottom: 20px; color: #666;">
                        You have been inactive for a while. Your session will expire in 5 minutes.
                        Click "Stay Logged In" to continue your session.
                    </p>
                    <button id="stay-logged-in" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i>   Stay Logged In</button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', warningHtml);
        
        // Handle stay logged in button
        document.getElementById('stay-logged-in').addEventListener('click', function() {
            document.getElementById('idle-warning-modal').remove();
            resetIdleTimer();
        });
    }
    
    /**
     * Perform logout
     */
    function performLogout() {
        // Get logout URL from data attribute or use default
        const logoutUrl = document.body.getAttribute('data-logout-url') || '/logout';
        
        // Create logout form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = logoutUrl;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
    
    /**
     * Initialize idle detection
     */
    function init() {
        // Only run on authenticated pages (check if logout route exists)
        if (!document.querySelector('meta[name="csrf-token"]')) {
            return;
        }
        
        // Set initial timers
        resetIdleTimer();
        
        // Attach activity listeners
        activityEvents.forEach(function(event) {
            document.addEventListener(event, resetIdleTimer, true);
        });
        
        // Also check periodically (in case events are missed)
        setInterval(function() {
            const timeSinceLastActivity = Date.now() - lastActivity;
            if (timeSinceLastActivity >= IDLE_TIMEOUT) {
                performLogout();
            }
        }, CHECK_INTERVAL);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

