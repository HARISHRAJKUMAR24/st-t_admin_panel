// =============================================
// SETTINGS SOCIAL - JAVASCRIPT
// =============================================

// =============================================
// CLEAR SOCIAL FIELD
// =============================================

window.clearSocial = function (key) {
    const input = document.getElementById('social_' + key);
    if (input) {
        input.value = '';
        input.focus();
    }
};

// =============================================
// FORM SUBMISSION - SAVE SOCIAL LINKS
// =============================================

document.addEventListener('DOMContentLoaded', function () {
    const socialForm = document.getElementById('socialSettingsForm');

    if (socialForm) {
        socialForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            // Prepare form data
            const formData = new FormData(this);

            // AJAX request
            fetch('ajax/update-social-settings.php', {
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
                            location.reload();
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