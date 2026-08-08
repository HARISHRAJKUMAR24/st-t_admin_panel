// =============================================
// SETTINGS PASSWORD - JAVASCRIPT
// =============================================

// =============================================
// TOGGLE PASSWORD VISIBILITY
// =============================================

window.togglePassword = function(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
        input.type = 'password';
        button.innerHTML = '<i class="bi bi-eye"></i>';
    }
};

// =============================================
// GENERATE RANDOM PASSWORD
// =============================================

window.generatePassword = function() {
    const length = 12;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
    let password = '';
    
    // Ensure at least one of each type
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numbers = '0123456789';
    const special = '!@#$%^&*()_+-=';
    
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
    password += numbers.charAt(Math.floor(Math.random() * numbers.length));
    password += special.charAt(Math.floor(Math.random() * special.length));
    
    // Fill remaining with random characters
    for (let i = 4; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    
    // Shuffle the password
    password = password.split('').sort(() => 0.5 - Math.random()).join('');
    
    // Set the password in the field
    const newPasswordInput = document.getElementById('newPassword');
    newPasswordInput.value = password;
    
    // Trigger validation
    validatePassword(password);
    checkConfirmPassword();
    
    // Auto-fill confirm password
    document.getElementById('confirmPassword').value = password;
    checkConfirmPassword();
    
    // Show success message
    Swal.fire({
        icon: 'success',
        title: 'Password Generated!',
        text: 'A strong password has been generated and filled in for you.',
        timer: 2000,
        showConfirmButton: false
    });
};

// =============================================
// VALIDATE PASSWORD STRENGTH
// =============================================

function validatePassword(password) {
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    // Check requirements
    const hasLength = password.length >= 8;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[^a-zA-Z0-9]/.test(password);
    
    // Update requirement indicators
    updateRequirement('reqLength', hasLength);
    updateRequirement('reqUppercase', hasUppercase);
    updateRequirement('reqLowercase', hasLowercase);
    updateRequirement('reqNumber', hasNumber);
    updateRequirement('reqSpecial', hasSpecial);
    
    if (password.length === 0) {
        strengthBar.className = 'password-strength';
        strengthText.textContent = 'Password must be at least 8 characters';
        strengthText.className = 'password-strength-text';
        return;
    }
    
    // Calculate strength score
    let score = 0;
    if (hasLength) score++;
    if (hasUppercase) score++;
    if (hasLowercase) score++;
    if (hasNumber) score++;
    if (hasSpecial) score++;
    if (password.length >= 12) score++;
    
    let level = '';
    let message = '';
    let className = '';
    
    if (score <= 2) {
        level = 'weak';
        message = 'Weak - Add more variety to your password';
        className = 'weak';
    } else if (score <= 4) {
        level = 'medium';
        message = 'Medium - Add uppercase and special characters';
        className = 'medium';
    } else if (score <= 5) {
        level = 'strong';
        message = 'Strong password!';
        className = 'strong';
    } else {
        level = 'very-strong';
        message = 'Very strong password!';
        className = 'very-strong';
    }
    
    strengthBar.className = 'password-strength ' + level;
    strengthText.textContent = message;
    strengthText.className = 'password-strength-text ' + className;
}

// =============================================
// UPDATE REQUIREMENT INDICATOR
// =============================================

function updateRequirement(id, met) {
    const element = document.getElementById(id);
    if (!element) return;
    
    if (met) {
        element.className = 'req met';
        element.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + element.textContent.replace('✓', '').trim();
    } else {
        element.className = 'req';
        element.innerHTML = '<i class="bi bi-circle"></i> ' + element.textContent.replace('✓', '').trim();
    }
}

// =============================================
// CHECK CONFIRM PASSWORD
// =============================================

function checkConfirmPassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorElement = document.getElementById('confirmPasswordError');
    const confirmInput = document.getElementById('confirmPassword');
    
    if (confirmPassword.length === 0) {
        errorElement.style.display = 'none';
        confirmInput.className = 'form-control';
        return;
    }
    
    if (newPassword === confirmPassword) {
        errorElement.style.display = 'none';
        confirmInput.className = 'form-control is-valid';
        return true;
    } else {
        errorElement.style.display = 'block';
        confirmInput.className = 'form-control is-invalid';
        return false;
    }
}

// =============================================
// CHECK CURRENT PASSWORD (AJAX)
// =============================================

function checkCurrentPassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const errorElement = document.getElementById('currentPasswordError');
    const currentInput = document.getElementById('currentPassword');
    
    if (currentPassword.length === 0) {
        errorElement.style.display = 'none';
        currentInput.className = 'form-control';
        return;
    }
    
    // Send AJAX request to validate current password
    const formData = new FormData();
    formData.append('current_password', currentPassword);
    formData.append('action', 'validate_current');
    
    return fetch('ajax/update-password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            errorElement.style.display = 'none';
            currentInput.className = 'form-control is-valid';
            return true;
        } else {
            errorElement.style.display = 'block';
            currentInput.className = 'form-control is-invalid';
            return false;
        }
    })
    .catch(() => {
        // If error, don't show validation
        errorElement.style.display = 'none';
        currentInput.className = 'form-control';
        return true;
    });
}

// =============================================
// EVENT LISTENERS
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // New password input - validate on input
    document.getElementById('newPassword').addEventListener('input', function() {
        validatePassword(this.value);
        checkConfirmPassword();
    });
    
    // Confirm password input - validate on input
    document.getElementById('confirmPassword').addEventListener('input', function() {
        checkConfirmPassword();
    });
    
    // Current password input - validate on blur
    document.getElementById('currentPassword').addEventListener('blur', function() {
        if (this.value.length > 0) {
            checkCurrentPassword();
        }
    });
    
    // Current password input - validate on input (for real-time feedback)
    document.getElementById('currentPassword').addEventListener('input', function() {
        if (this.value.length > 0) {
            checkCurrentPassword();
        } else {
            document.getElementById('currentPasswordError').style.display = 'none';
            this.className = 'form-control';
        }
    });
    
    // =============================================
    // FORM SUBMISSION
    // =============================================
    
    const passwordForm = document.getElementById('passwordForm');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get values
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validate current password
            if (!currentPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter your current password'
                });
                document.getElementById('currentPassword').focus();
                return;
            }
            
            // Validate new password
            if (!newPassword || newPassword.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'New password must be at least 8 characters long'
                });
                document.getElementById('newPassword').focus();
                return;
            }
            
            // Validate confirm password
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Passwords do not match'
                });
                document.getElementById('confirmPassword').focus();
                return;
            }
            
            // Check if new password is same as current
            if (currentPassword === newPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'New password cannot be the same as current password'
                });
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            
            // AJAX request
            fetch('ajax/update-password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Clear form
                            document.getElementById('currentPassword').value = '';
                            document.getElementById('newPassword').value = '';
                            document.getElementById('confirmPassword').value = '';
                            document.getElementById('passwordStrength').className = 'password-strength';
                            document.getElementById('passwordStrengthText').textContent = 'Password must be at least 8 characters';
                            document.getElementById('passwordStrengthText').className = 'password-strength-text';
                            document.getElementById('currentPassword').className = 'form-control';
                            document.getElementById('newPassword').className = 'form-control';
                            document.getElementById('confirmPassword').className = 'form-control';
                            
                            // Reset requirements
                            ['reqLength', 'reqUppercase', 'reqLowercase', 'reqNumber', 'reqSpecial'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) {
                                    el.className = 'req';
                                    el.innerHTML = '<i class="bi bi-circle"></i> ' + el.textContent.replace('✓', '').trim();
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again.'
                    });
                });
        });
    }
});