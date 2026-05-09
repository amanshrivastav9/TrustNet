/**
 * TrustNet Main JavaScript
 * Common functions and utilities
 */

// CSRF token handling
function getCSRFToken() {
    return document.querySelector('input[name="csrf_token"]')?.value;
}

// AJAX wrapper
function trustNetAjax(url, method, data, successCallback, errorCallback) {
    const csrfToken = getCSRFToken();
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (successCallback) successCallback(data);
        } else {
            if (errorCallback) errorCallback(data);
            else showNotification(data.message || 'An error occurred', 'error');
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        if (errorCallback) errorCallback(error);
        else showNotification('Network error occurred', 'error');
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Show animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
    
    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    });
}

// Form validation
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const errors = [];
    
    for (const field in rules) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) continue;
        
        const value = input.value.trim();
        const rule = rules[field];
        
        if (rule.required && !value) {
            errors.push(`${rule.label || field} is required`);
            isValid = false;
        }
        
        if (rule.minLength && value.length < rule.minLength) {
            errors.push(`${rule.label || field} must be at least ${rule.minLength} characters`);
            isValid = false;
        }
        
        if (rule.pattern && !rule.pattern.test(value)) {
            errors.push(`${rule.label || field} is invalid`);
            isValid = false;
        }
        
        if (rule.match && value !== form.querySelector(`[name="${rule.match}"]`).value) {
            errors.push(`${rule.label || field} does not match`);
            isValid = false;
        }
    }
    
    if (!isValid) {
        showNotification(errors.join('\n'), 'error');
    }
    
    return isValid;
}

// Loading spinner
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const originalHtml = element.innerHTML;
        element.setAttribute('data-original-html', originalHtml);
        element.innerHTML = '<span class="spinner"></span> Loading...';
        element.disabled = true;
    }
}

function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const originalHtml = element.getAttribute('data-original-html');
        if (originalHtml) {
            element.innerHTML = originalHtml;
            element.removeAttribute('data-original-html');
        }
        element.disabled = false;
    }
}

// Copy to clipboard
function copyToClipboard(text, successMessage = 'Copied to clipboard!') {
    navigator.clipboard.writeText(text).then(() => {
        showNotification(successMessage, 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

// Dark/Light theme toggle
function toggleTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('trustnet_theme', newTheme);
    
    showNotification(`Theme changed to ${newTheme} mode`, 'info');
}

// Load saved theme
function loadTheme() {
    const savedTheme = localStorage.getItem('trustnet_theme') || 'dark';
    document.body.setAttribute('data-theme', savedTheme);
}

// Auto logout after inactivity
let inactivityTimer;
const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        showNotification('Session expired due to inactivity', 'warning');
        setTimeout(() => {
            window.location.href = '/trustnet/logout.php';
        }, 3000);
    }, INACTIVITY_TIMEOUT);
}

function setupInactivityTimer() {
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    events.forEach(event => {
        document.addEventListener(event, resetInactivityTimer);
    });
    resetInactivityTimer();
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    loadTheme();
    setupInactivityTimer();
    
    // Add CSS for notifications and spinner
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background: linear-gradient(135deg, #0A0F1C, #0F172A);
            border: 1px solid rgba(0, 209, 255, 0.3);
            border-radius: 10px;
            padding: 15px;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            border-color: #00D1FF;
            color: #00D1FF;
        }
        
        .notification-error {
            border-color: #ff4757;
            color: #ff4757;
        }
        
        .notification-warning {
            border-color: #ffa502;
            color: #ffa502;
        }
        
        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 20px;
            cursor: pointer;
            padding: 0 5px;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #00D1FF;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .pulse {
            animation: pulse 1s ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    `;
    document.head.appendChild(style);
});