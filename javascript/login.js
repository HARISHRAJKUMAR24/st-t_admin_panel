// =============================================
// LOGIN AJAX HANDLER WITH TOAST NOTIFICATIONS
// =============================================

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const loginBtn = document.getElementById('loginBtn');
    const loginText = document.getElementById('loginText');
    const loginSpinner = document.getElementById('loginSpinner');

    // Password visibility toggle
    window.togglePassword = function () {
        const toggleIcon = document.getElementById('toggleIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    };

    // Toast notification system
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;

        // Icons based on type
        const icons = {
            success: 'bi bi-check-circle-fill',
            error: 'bi bi-exclamation-circle-fill',
            info: 'bi bi-info-circle-fill'
        };

        toast.innerHTML = `
            <span class="toast-icon"><i class="${icons[type] || icons.info}"></i></span>
            <span>${message}</span>
            <button class="toast-close" onclick="this.closest('.toast-notification').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        container.appendChild(toast);

        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 500);
            }
        }, 4000);
    }

    // Login form submission
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        // Validate inputs
        if (!email || !password) {
            showToast('Please fill in all fields', 'error');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Please enter a valid email address', 'error');
            return;
        }

        // Show loading state
        loginBtn.disabled = true;
        loginText.style.display = 'none';
        loginSpinner.style.display = 'inline-block';

        // Prepare form data
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);

        // AJAX request to login handler
        fetch('ajax/login.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Hide loading state
                loginBtn.disabled = false;
                loginText.style.display = 'inline';
                loginSpinner.style.display = 'none';

                if (data.success) {
                    showToast(data.message, 'success');
                    // Redirect after successful login
                    setTimeout(() => {
                        window.location.href = data.redirect || 'index.php';
                    }, 1000);
                } else {
                    showToast(data.message, 'error');
                    // Clear password field on error
                    passwordInput.value = '';
                    passwordInput.focus();
                }
            })
            .catch(error => {
                // Hide loading state
                loginBtn.disabled = false;
                loginText.style.display = 'inline';
                loginSpinner.style.display = 'none';

                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            });
    });

    // Enter key support for form submission
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const activeElement = document.activeElement;
            if (activeElement === emailInput || activeElement === passwordInput) {
                loginForm.dispatchEvent(new Event('submit'));
            }
        }
    });
});