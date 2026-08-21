<!-- =========================================================
     DREAM CTA SECTION
========================================================= -->

<section class="dream-cta-section">

    <div class="dream-cta-card">

        <!-- IMAGE STACK -->
        <div class="cta-photo-stack">

            <img
                src="<?= SITE_URL; ?>assets/images/cta-1.jpeg"
                class="cta-photo active"
                alt="Travel destination">

            <img
                src="<?= SITE_URL; ?>assets/images/cta-2.jpeg"
                class="cta-photo"
                alt="Travel destination">

            <img
                src="<?= SITE_URL; ?>assets/images/cta-3.jpeg"
                class="cta-photo"
                alt="Travel destination">

        </div>


        <!-- CONTENT -->
        <div class="dream-cta-content">

            <h2>
                Ready to Plan Your
                <span>Dream</span>
                Journey?
            </h2>

            <button
                class="dream-cta-button"
                id="openCtaModal"
                type="button">
                <span>Send us a Message</span>

                <span class="dream-cta-arrow">
                    <i class="bi bi-arrow-up-right"></i>
                </span>

            </button>

        </div>


        <!-- BUS -->
        <div class="cta-plane">

            <img
                src="<?= SITE_URL; ?>assets/images/aeroplane.png"
                alt="Bus">

        </div>

    </div>

</section>


<!-- =========================================================
     CTA POPUP MODAL
========================================================= -->

<div
    class="cta-modal-overlay"
    id="ctaModal"
    aria-hidden="true">

    <div
        class="cta-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ctaModalTitle">

        <!-- CLOSE BUTTON -->
        <button
            type="button"
            class="cta-modal-close"
            id="ctaModalClose"
            aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>


        <!-- HEADER -->
        <div class="cta-modal-header">

            <div class="cta-modal-icon">
                <i class="bi bi-send-fill"></i>
            </div>

            <h3 id="ctaModalTitle">
                Send us a Message
            </h3>

            <p>
                Tell us about your dream trip —
                we'll get back to you within hours.
            </p>

        </div>


        <!-- FORM -->
        <form
            class="cta-modal-form"
            id="ctaForm"
            novalidate>

            <!-- NAME -->
            <div class="cta-form-group">

                <label for="ctaName">
                    Full Name
                </label>

                <div class="cta-input-wrap">

                    <i class="bi bi-person"></i>

                    <input
                        type="text"
                        id="ctaName"
                        name="full_name"
                        placeholder="Enter your full name"
                        autocomplete="name"
                        required>

                </div>

                <span
                    class="cta-error"
                    id="ctaNameError"></span>

            </div>


            <!-- PHONE -->
            <div class="cta-form-group">

                <label for="ctaPhone">
                    Mobile Number
                </label>

                <div class="cta-input-wrap">

                    <i class="bi bi-telephone"></i>

                    <input
                        type="tel"
                        id="ctaPhone"
                        name="phone_number"
                        placeholder="Enter 10-digit mobile number"
                        autocomplete="tel"
                        maxlength="10"
                        inputmode="numeric"
                        required>

                </div>

                <span
                    class="cta-error"
                    id="ctaPhoneError"></span>

            </div>


            <!-- MESSAGE -->
            <div class="cta-form-group">

                <label for="ctaMessage">
                    Your Message
                </label>

                <div class="cta-input-wrap cta-textarea-wrap">

                    <i class="bi bi-chat-dots"></i>

                    <textarea
                        id="ctaMessage"
                        name="message"
                        rows="3"
                        placeholder="Where do you want to go? Any preferences?"
                        required></textarea>

                </div>

                <span
                    class="cta-error"
                    id="ctaMessageError"></span>

            </div>


            <!-- LOADER -->
            <div
                class="cta-modal-loader"
                id="ctaLoader"
                style="display:none;">

                <div class="cta-spinner"></div>

                <span>
                    Sending your message...
                </span>

            </div>


            <!-- SUBMIT -->
            <button
                type="submit"
                class="cta-modal-submit"
                id="ctaSubmitBtn">

                <span>
                    Send Message
                </span>

                <span class="cta-submit-icon">
                    <i class="bi bi-arrow-up-right"></i>
                </span>

            </button>

        </form>


        <!-- SUCCESS -->
        <div
            class="cta-modal-success"
            id="ctaSuccess"
            style="display:none;">

            <div class="cta-success-check">

                <i class="bi bi-check-lg"></i>

            </div>

            <h4>
                Message Sent!
            </h4>

            <p>
                Our travel expert will contact you shortly.
            </p>

            <button
                type="button"
                class="cta-success-close"
                id="ctaSuccessClose">
                Got it!
            </button>

        </div>

    </div>

</div>


<!-- =========================================================
     CTA MODAL CSS
========================================================= -->
<style>

/* =========================================================
   MODAL OVERLAY
======================================================== */

.cta-modal-overlay {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.65);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    z-index: 999999;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.cta-modal-overlay.cta-modal-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}


/* =========================================================
   MODAL BOX
======================================================== */

.cta-modal {
    position: relative;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    transform: translateY(35px) scale(0.96);
    opacity: 0;
    transition: transform 0.35s ease, opacity 0.35s ease;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
}

.cta-modal-overlay.cta-modal-open .cta-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.cta-modal::-webkit-scrollbar {
    width: 4px;
}

.cta-modal::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.cta-modal::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}


/* =========================================================
   CLOSE BUTTON
======================================================== */

.cta-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 50%;
    background: #f3f4f6;
    color: #333;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.2s ease, transform 0.2s ease;
    z-index: 10;
}

.cta-modal-close:hover {
    background: #e5e7eb;
    transform: rotate(90deg);
}


/* =========================================================
   HEADER
======================================================== */

.cta-modal-header {
    text-align: center;
    padding: 5px 35px 25px;
}

.cta-modal-icon {
    width: 58px;
    height: 58px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f3f4f6;
    font-size: 23px;
}

.cta-modal-header h3 {
    margin: 0 0 8px;
    font-size: 26px;
    font-weight: 700;
    color: #111827;
}

.cta-modal-header p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: #6b7280;
}


/* =========================================================
   FORM
======================================================== */

.cta-modal-form {
    width: 100%;
}

.cta-form-group {
    margin-bottom: 18px;
}

.cta-form-group label {
    display: block;
    margin-bottom: 7px;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}


/* =========================================================
   INPUT
======================================================== */

.cta-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.cta-input-wrap > i {
    position: absolute;
    left: 15px;
    color: #9ca3af;
    font-size: 17px;
    pointer-events: none;
    transition: color 0.2s ease;
}

.cta-input-wrap input,
.cta-input-wrap textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 12px;
    outline: none;
    color: #111827;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.cta-input-wrap input {
    height: 48px;
    padding: 0 15px 0 44px;
}

.cta-input-wrap textarea {
    min-height: 90px;
    padding: 13px 15px 13px 44px;
    resize: vertical;
}

.cta-input-wrap input:focus,
.cta-input-wrap textarea:focus {
    background: #ffffff;
    border-color: #111827;
    box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.08);
}

.cta-input-wrap input:focus + i,
.cta-input-wrap textarea:focus + i {
    color: #111827;
}

.cta-textarea-wrap {
    align-items: flex-start;
}

.cta-textarea-wrap > i {
    top: 15px;
}


/* =========================================================
   ERROR
======================================================== */

.cta-error {
    display: block;
    min-height: 0;
    margin-top: 5px;
    font-size: 12px;
    color: #dc2626;
    transition: min-height 0.2s ease;
}

.cta-error.cta-error-visible {
    min-height: 16px;
}

.cta-input-error input,
.cta-input-error textarea {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
}

.cta-input-error > i {
    color: #dc2626 !important;
}


/* =========================================================
   SUBMIT BUTTON
======================================================== */

.cta-modal-submit {
    width: 100%;
    min-height: 52px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 8px 0 20px;
    border: none;
    border-radius: 13px;
    background: #111827;
    color: #ffffff;
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
}

.cta-modal-submit:hover {
    background: #000000;
    transform: translateY(-1px);
}

.cta-submit-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.12);
}


/* =========================================================
   LOADER
======================================================== */

.cta-modal-loader {
    display: none;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px;
    margin-bottom: 12px;
    border-radius: 10px;
    background: #f9fafb;
    color: #4b5563;
    font-size: 13px;
}

.cta-modal-loader.cta-loader-visible {
    display: flex;
}

.cta-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid #d1d5db;
    border-top-color: #111827;
    border-radius: 50%;
    animation: ctaSpinner 0.7s linear infinite;
}

@keyframes ctaSpinner {
    to { transform: rotate(360deg); }
}


/* =========================================================
   SUCCESS
======================================================== */

.cta-modal-success {
    display: none;
    text-align: center;
    padding: 20px 10px 10px;
}

.cta-modal-success.cta-success-visible {
    display: block;
    animation: ctaSuccessIn 0.5s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
}

@keyframes ctaSuccessIn {
    0% { opacity: 0; transform: scale(0.9) translateY(10px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

.cta-success-check {
    width: 65px;
    height: 65px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #ecfdf5;
    color: #059669;
    font-size: 28px;
}

.cta-modal-success h4 {
    margin: 0 0 8px;
    font-size: 23px;
    color: #111827;
}

.cta-modal-success p {
    margin: 0 0 20px;
    color: #6b7280;
    font-size: 14px;
}

.cta-success-close {
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    background: #111827;
    color: #ffffff;
    cursor: pointer;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    transition: background 0.2s ease;
}

.cta-success-close:hover {
    background: #333;
}


/* =========================================================
   GENERAL ERROR
======================================================== */

.cta-general-error {
    margin-bottom: 15px;
    padding: 12px;
    border-radius: 9px;
    background: #fef2f2;
    color: #dc2626;
    font-size: 13px;
    animation: ctaErrorShake 0.4s ease;
}

@keyframes ctaErrorShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}


/* =========================================================
   BODY LOCK
======================================================== */

body.cta-modal-active {
    overflow: hidden !important;
}


/* =========================================================
   MODAL RESPONSIVE — TABLET
======================================================== */

@media (max-width: 768px) {

    .cta-modal {
        max-width: 460px;
        padding: 28px 24px;
        border-radius: 22px;
    }

    .cta-modal-header {
        padding: 5px 28px 22px;
    }

    .cta-modal-header h3 {
        font-size: 23px;
    }

    .cta-modal-icon {
        width: 52px;
        height: 52px;
        font-size: 20px;
    }

}


/* =========================================================
   MODAL RESPONSIVE — MOBILE
======================================================== */

@media (max-width: 576px) {

    .cta-modal-overlay {
        padding: 12px;
        align-items: flex-end;
    }

    .cta-modal {
        max-width: 100%;
        max-height: 88vh;
        max-height: 88dvh;
        border-radius: 24px 24px 0 0;
        padding: 24px 20px 28px;
        transform: translateY(100%);
        opacity: 1;
    }

    .cta-modal-overlay.cta-modal-open .cta-modal {
        transform: translateY(0);
        opacity: 1;
    }

    /* Drag handle */
    .cta-modal::before {
        content: '';
        position: sticky;
        top: 0;
        display: block;
        width: 36px;
        height: 4px;
        margin: 0 auto 16px;
        background: #ddd;
        border-radius: 100px;
        z-index: 5;
    }

    .cta-modal-close {
        top: 14px;
        right: 14px;
        width: 36px;
        height: 36px;
        font-size: 14px;
    }

    .cta-modal-header {
        padding: 0 30px 20px;
    }

    .cta-modal-header h3 {
        font-size: 21px;
    }

    .cta-modal-header p {
        font-size: 13px;
    }

    .cta-modal-icon {
        width: 48px;
        height: 48px;
        font-size: 18px;
        margin-bottom: 12px;
    }

    .cta-form-group {
        margin-bottom: 16px;
    }

    .cta-form-group label {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .cta-input-wrap input {
        height: 46px;
        font-size: 14px;
        padding: 0 14px 0 42px;
    }

    .cta-input-wrap textarea {
        min-height: 80px;
        font-size: 14px;
        padding: 12px 14px 12px 42px;
    }

    .cta-input-wrap > i {
        left: 14px;
        font-size: 16px;
    }

    .cta-modal-submit {
        min-height: 50px;
        font-size: 14px;
        padding: 0 6px 0 18px;
        border-radius: 14px;
    }

    .cta-submit-icon {
        width: 36px;
        height: 36px;
    }

    .cta-success-check {
        width: 58px;
        height: 58px;
        font-size: 24px;
    }

    .cta-modal-success h4 {
        font-size: 20px;
    }

    .cta-modal-success p {
        font-size: 13px;
    }

    .cta-success-close {
        padding: 11px 28px;
        font-size: 14px;
    }

}


/* =========================================================
   MODAL RESPONSIVE — SMALL MOBILE
======================================================== */

@media (max-width: 400px) {

    .cta-modal-overlay {
        padding: 0;
    }

    .cta-modal {
        padding: 20px 16px 24px;
        max-height: 90vh;
        max-height: 90dvh;
    }

    .cta-modal::before {
        margin-bottom: 12px;
    }

    .cta-modal-close {
        top: 12px;
        right: 12px;
        width: 32px;
        height: 32px;
        font-size: 13px;
    }

    .cta-modal-header {
        padding: 0 24px 18px;
    }

    .cta-modal-header h3 {
        font-size: 19px;
    }

    .cta-modal-header p {
        font-size: 12px;
    }

    .cta-modal-icon {
        width: 42px;
        height: 42px;
        font-size: 16px;
    }

    .cta-form-group {
        margin-bottom: 14px;
    }

    .cta-form-group label {
        font-size: 12px;
    }

    .cta-input-wrap input {
        height: 44px;
        font-size: 13px;
        padding: 0 12px 0 38px;
        border-radius: 10px;
    }

    .cta-input-wrap textarea {
        min-height: 72px;
        font-size: 13px;
        padding: 10px 12px 10px 38px;
        border-radius: 10px;
    }

    .cta-input-wrap > i {
        left: 12px;
        font-size: 14px;
    }

    .cta-textarea-wrap > i {
        top: 12px;
    }

    .cta-modal-submit {
        min-height: 46px;
        font-size: 13px;
        padding: 0 5px 0 16px;
        border-radius: 12px;
    }

    .cta-submit-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
    }

    .cta-error {
        font-size: 11px;
    }

    .cta-success-check {
        width: 50px;
        height: 50px;
        font-size: 22px;
    }

    .cta-modal-success h4 {
        font-size: 18px;
    }

    .cta-success-close {
        padding: 10px 24px;
        font-size: 13px;
    }

}


/* =========================================================
   MODAL RESPONSIVE — VERY SMALL
======================================================== */

@media (max-width: 350px) {

    .cta-modal {
        padding: 18px 14px 22px;
    }

    .cta-modal-header {
        padding: 0 20px 16px;
    }

    .cta-modal-header h3 {
        font-size: 17px;
    }

    .cta-input-wrap input {
        height: 42px;
        font-size: 12px;
        padding: 0 10px 0 36px;
    }

    .cta-input-wrap textarea {
        min-height: 65px;
        font-size: 12px;
        padding: 10px 10px 10px 36px;
    }

    .cta-input-wrap > i {
        font-size: 13px;
    }

    .cta-modal-submit {
        min-height: 44px;
        font-size: 12px;
    }

}


/* =========================================================
   LANDSCAPE PHONE MODAL
======================================================== */

@media (max-height: 500px) and (orientation: landscape) {

    .cta-modal-overlay {
        padding: 10px;
        align-items: center;
    }

    .cta-modal {
        max-height: calc(100vh - 20px);
        max-height: calc(100dvh - 20px);
        border-radius: 20px;
        padding: 18px 20px 16px;
        transform: translateY(35px) scale(0.96);
        opacity: 0;
    }

    .cta-modal-overlay.cta-modal-open .cta-modal {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    /* No bottom sheet in landscape */
    .cta-modal::before {
        display: none;
    }

    .cta-modal-header {
        padding: 0 30px 14px;
        margin-bottom: 10px;
    }

    .cta-modal-header h3 {
        font-size: 18px;
    }

    .cta-modal-header p {
        font-size: 12px;
    }

    .cta-modal-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .cta-form-group {
        margin-bottom: 10px;
    }

    .cta-input-wrap input {
        height: 40px;
        font-size: 13px;
        padding: 0 12px 0 36px;
    }

    .cta-input-wrap textarea {
        min-height: 50px;
        font-size: 13px;
        padding: 8px 12px 8px 36px;
    }

    .cta-input-wrap > i {
        font-size: 14px;
    }

    .cta-modal-submit {
        min-height: 40px;
        font-size: 13px;
        padding: 0 6px 0 16px;
    }

    .cta-submit-icon {
        width: 32px;
        height: 32px;
    }

    .cta-error {
        min-height: 0 !important;
        font-size: 10px;
    }

}

</style>

<!-- =========================================================
     CTA MODAL JAVASCRIPT
========================================================= -->

<script>
    (function() {

        /* =====================================================
           DOM ELEMENTS
           ===================================================== */

        const modal = document.getElementById('ctaModal');
        const closeBtn = document.getElementById('ctaModalClose');
        const successClose = document.getElementById('ctaSuccessClose');
        const form = document.getElementById('ctaForm');
        const success = document.getElementById('ctaSuccess');
        const loader = document.getElementById('ctaLoader');
        const submitBtn = document.getElementById('ctaSubmitBtn');
        const nameInput = document.getElementById('ctaName');
        const phoneInput = document.getElementById('ctaPhone');
        const messageInput = document.getElementById('ctaMessage');
        const nameError = document.getElementById('ctaNameError');
        const phoneError = document.getElementById('ctaPhoneError');
        const messageError = document.getElementById('ctaMessageError');


        /* =====================================================
           IMPORTANT FIX
           Move popup directly under BODY
           ===================================================== */

        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }


        /* Safety check */

        if (!modal) {
            console.error('CTA Modal: #ctaModal not found.');
            return;
        }


        /* =====================================================
           OPEN MODAL
           ===================================================== */

        document.addEventListener('click', function(e) {

            const openBtn = e.target.closest('#openCtaModal');

            if (!openBtn) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            modal.classList.add('cta-modal-open');
            modal.setAttribute('aria-hidden', 'false');

            document.body.classList.add('cta-modal-active');

            setTimeout(function() {

                if (nameInput) {
                    nameInput.focus();
                }

            }, 350);

        });

        /* =====================================================
           CLOSE MODAL
        ===================================================== */

        function closeModal() {

            modal.classList.remove('cta-modal-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('cta-modal-active');

            setTimeout(function() {
                resetModal();
            }, 300);

        }


        /* =====================================================
           RESET MODAL
        ===================================================== */

        function resetModal() {

            if (form) {
                form.reset();
                form.style.display = 'block';
            }

            if (success) {
                success.style.display = 'none';
                success.classList.remove('cta-success-visible');
            }

            if (loader) {
                loader.style.display = 'none';
                loader.classList.remove('cta-loader-visible');
            }

            if (submitBtn) {
                submitBtn.style.display = 'flex';
            }

            clearErrors();

        }


        /* =====================================================
           CLOSE BUTTON
        ===================================================== */

        if (closeBtn) {

            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });

        }


        /* =====================================================
           SUCCESS CLOSE
        ===================================================== */

        if (successClose) {

            successClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });

        }


        /* =====================================================
           CLICK OUTSIDE
        ===================================================== */

        modal.addEventListener('click', function(e) {

            if (e.target === modal) {
                closeModal();
            }

        });


        /* =====================================================
           ESCAPE KEY
        ===================================================== */

        document.addEventListener('keydown', function(e) {

            if (e.key === 'Escape' && modal.classList.contains('cta-modal-open')) {
                closeModal();
            }

        });


        /* =====================================================
           CLEAR ERRORS
        ===================================================== */

        function clearErrors() {

            if (nameError) {
                nameError.textContent = '';
                nameError.classList.remove('cta-error-visible');
            }

            if (phoneError) {
                phoneError.textContent = '';
                phoneError.classList.remove('cta-error-visible');
            }

            if (messageError) {
                messageError.textContent = '';
                messageError.classList.remove('cta-error-visible');
            }

            document.querySelectorAll('.cta-input-wrap').forEach(function(wrap) {
                wrap.classList.remove('cta-input-error');
            });

        }


        /* =====================================================
           SHOW ERROR
        ===================================================== */

        function showError(input, errorElement, message) {

            if (!input || !errorElement) return;

            errorElement.textContent = message;
            errorElement.classList.add('cta-error-visible');

            var wrapper = input.closest('.cta-input-wrap');

            if (wrapper) {
                wrapper.classList.add('cta-input-error');
            }

            input.focus();

        }


        /* =====================================================
           NAME VALIDATION
        ===================================================== */

        if (nameInput) {

            nameInput.addEventListener('input', function() {

                if (this.value.trim().length >= 2) {

                    var wrapper = this.closest('.cta-input-wrap');
                    if (wrapper) wrapper.classList.remove('cta-input-error');

                    nameError.textContent = '';
                    nameError.classList.remove('cta-error-visible');
                }

            });

        }


        /* =====================================================
           PHONE VALIDATION
        ===================================================== */

        if (phoneInput) {

            phoneInput.addEventListener('input', function() {

                this.value = this.value.replace(/\D/g, '');

                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }

                if (this.value.length === 10) {

                    var wrapper = this.closest('.cta-input-wrap');
                    if (wrapper) wrapper.classList.remove('cta-input-error');

                    phoneError.textContent = '';
                    phoneError.classList.remove('cta-error-visible');
                }

            });

        }


        /* =====================================================
           MESSAGE VALIDATION
        ===================================================== */

        if (messageInput) {

            messageInput.addEventListener('input', function() {

                if (this.value.trim().length >= 5) {

                    var wrapper = this.closest('.cta-input-wrap');
                    if (wrapper) wrapper.classList.remove('cta-input-error');

                    messageError.textContent = '';
                    messageError.classList.remove('cta-error-visible');
                }

            });

        }


        /* =====================================================
           FORM SUBMIT
        ===================================================== */

        if (form) {

            form.addEventListener('submit', function(e) {

                e.preventDefault();
                clearErrors();

                var name = nameInput ? nameInput.value.trim() : '';
                var phone = phoneInput ? phoneInput.value.trim() : '';
                var message = messageInput ? messageInput.value.trim() : '';

                var valid = true;


                /* NAME */
                if (name.length < 2) {
                    showError(nameInput, nameError, 'Please enter your full name.');
                    valid = false;
                } else if (!/^[a-zA-Z\s.'-]{2,100}$/.test(name)) {
                    showError(nameInput, nameError, 'Name can only contain letters, spaces, dots, hyphens, and apostrophes.');
                    valid = false;
                }


                /* PHONE */
                if (!phone) {
                    showError(phoneInput, phoneError, 'Please enter your mobile number.');
                    valid = false;
                } else if (!/^[6-9][0-9]{9}$/.test(phone)) {
                    showError(phoneInput, phoneError, 'Please enter a valid 10-digit mobile number.');
                    valid = false;
                }


                /* MESSAGE */
                if (!message) {
                    showError(messageInput, messageError, 'Please enter your message.');
                    valid = false;
                } else if (message.length < 5) {
                    showError(messageInput, messageError, 'Message must be at least 5 characters.');
                    valid = false;
                } else if (message.length > 500) {
                    showError(messageInput, messageError, 'Message cannot exceed 500 characters.');
                    valid = false;
                }


                if (!valid) return;


                /* LOADER */
                if (loader) {
                    loader.style.display = 'flex';
                    loader.classList.add('cta-loader-visible');
                }

                if (submitBtn) {
                    submitBtn.style.display = 'none';
                }


                /* FORM DATA */
                var formData = new FormData();
                formData.append('full_name', name);
                formData.append('phone_number', phone);
                formData.append('message', message);


                /* AJAX */
                fetch('<?= SITE_URL; ?>ajax/cta-message.php', {
                        method: 'POST',
                        body: formData
                    })

                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP Error: ' + response.status);
                        return response.json();
                    })

                    .then(function(data) {

                        if (loader) {
                            loader.style.display = 'none';
                            loader.classList.remove('cta-loader-visible');
                        }

                        if (submitBtn) {
                            submitBtn.style.display = 'flex';
                        }

                        if (data.success) {
                            form.style.display = 'none';
                            success.style.display = 'block';
                            success.classList.add('cta-success-visible');

                            setTimeout(function() {
                                closeModal();
                            }, 5000);
                        } else {
                            showServerError(data.message || 'Unable to send your message.');
                        }

                    })

                    .catch(function(error) {

                        console.error('CTA Error:', error);

                        if (loader) {
                            loader.style.display = 'none';
                            loader.classList.remove('cta-loader-visible');
                        }

                        if (submitBtn) {
                            submitBtn.style.display = 'flex';
                        }

                        showServerError('Network error. Please check your connection and try again.');

                    });

            });

        }


        /* =====================================================
           SERVER ERROR
        ===================================================== */

        function showServerError(message) {

            if (!form) return;

            var oldError = form.querySelector('.cta-general-error');
            if (oldError) oldError.remove();

            var errorDiv = document.createElement('div');
            errorDiv.className = 'cta-general-error';
            errorDiv.textContent = message;
            form.prepend(errorDiv);

            setTimeout(function() {
                if (errorDiv && errorDiv.parentNode) errorDiv.remove();
            }, 5000);

        }

    })();
</script>