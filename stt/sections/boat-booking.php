<!-- =========================================================
     BOAT BOOKING · FULL WIDTH SECTION
========================================================= -->

<section class="boat-booking-section">

  <!-- DECORATIVE WAVES -->
  <div class="boat-wave boat-wave-one"></div>
  <div class="boat-wave boat-wave-two"></div>

  <!-- FLOATING DOTS -->
  <span class="boat-dot boat-dot-one"></span>
  <span class="boat-dot boat-dot-two"></span>
  <span class="boat-dot boat-dot-three"></span>


  <!-- =====================================================
       LEFT CONTENT
  ====================================================== -->

  <div class="boat-copy">

    <div class="boat-eyebrow">
      <span>✦</span>
      YOUR JOURNEY ON WATER
    </div>


    <h2 class="boat-title">
      Sail
      <em>Your Way</em>
    </h2>


    <p class="boat-description">
      Discover peaceful waters, beautiful coastlines and
      unforgettable journeys with our comfortable boat
      experiences.
    </p>


    <!-- FEATURES -->

    <div class="boat-features">

      <div class="boat-feature">

        <span class="boat-number">
          01
        </span>

        <div>
          <strong>Comfortable Boats</strong>
          <small>Relax and enjoy every moment on the water.</small>
        </div>

      </div>


      <div class="boat-feature">

        <span class="boat-number">
          02
        </span>

        <div>
          <strong>Flexible Trips</strong>
          <small>Choose short rides or complete day journeys.</small>
        </div>

      </div>


      <div class="boat-feature">

        <span class="boat-number">
          03
        </span>

        <div>
          <strong>Scenic Routes</strong>
          <small>Explore beautiful destinations from the water.</small>
        </div>

      </div>

    </div>


    <!-- BOOK BUTTON -->

    <a
      href="#"
      class="boat-book-button">

      <span>
        Book a Boat
      </span>

      <b>
        ↗
      </b>

    </a>

  </div>



  <!-- =====================================================
       RIGHT WATER VISUAL
  ====================================================== -->

  <div class="boat-visual">

    <!-- BIG WATER SHAPE -->
    <div class="boat-water-circle"></div>


    <!-- BOAT IMAGE -->

    <div class="boat-image">

      <img
        src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1400&q=90"
        alt="Boat travel experience">

    </div>


    <!-- FLOATING DESTINATION -->

    <div class="boat-destination">

      <span>✦</span>

      <div>
        <strong>Explore The Water</strong>
        <small>Beautiful journeys await</small>
      </div>

    </div>


    <!-- FLOATING BOOKING CARD -->

    <div class="boat-book-card">

      <div class="boat-card-top">

        <span>BOAT EXPERIENCE</span>

        <i></i>

      </div>


      <strong class="boat-card-title">
        Your Boat Awaits
      </strong>


      <p>
        Cruise • Explore • Relax
      </p>


      <div class="boat-card-bottom">

        <span>Starting from</span>

        <strong>
          ₹999
        </strong>

      </div>

    </div>


    <!-- LITTLE WAVE BADGE -->

    <div class="boat-wave-badge">

      <span>≈</span>

      <div>
        <strong>Peaceful Waters</strong>
        <small>Perfect for your getaway</small>
      </div>

    </div>

  </div>


  <!-- BOTTOM LABEL -->

  <div class="boat-bottom-label">
    <span>✦</span>
    SAIL • EXPLORE • DISCOVER
  </div>

</section>


<!-- =========================================================
     POPUP MODAL (NAME + MOBILE ONLY)
========================================================== -->
<div class="boat-booking-modal" id="boatBookingModal">
  <div class="boat-modal-content">
    <button class="boat-modal-close" id="closeBoatModal">&times;</button>
    <h3>Book Your Boat</h3>
    <p>Enter your details and we will contact you shortly.</p>

    <form id="boatBookingForm">
      <div class="boat-form-group">
        <label>Your Name</label>
        <input type="text" name="customer_name" id="boatCustomerName" placeholder="Enter your full name" required>
      </div>

      <div class="boat-form-group">
        <label>Mobile Number</label>
        <input type="tel" name="mobile" id="boatMobile" placeholder="Enter your mobile number" required>
      </div>

      <button type="submit" class="boat-submit-btn">Submit Booking</button>
      <div class="boat-form-message" id="boatFormMessage"></div>
    </form>
  </div>
</div>


<style>
  /* =========================================================
   MAIN SECTION
========================================================= */

  .boat-booking-section {

    position: relative;

    width: 100%;

    min-height: 760px;

    overflow: hidden;

    background: #eaf7f8;

    display: grid;

    grid-template-columns: 44% 56%;

    align-items: center;

    box-sizing: border-box;

    isolation: isolate;

  }


  /* =========================================================
   BACKGROUND WAVES
========================================================= */

  .boat-wave {

    position: absolute;

    border: 1px dashed rgba(49, 116, 125, .18);

    border-radius: 50%;

    pointer-events: none;

  }


  .boat-wave-one {

    width: 700px;

    height: 300px;

    right: -100px;

    bottom: -80px;

    transform:
      rotate(-8deg);

  }


  .boat-wave-two {

    width: 520px;

    height: 220px;

    right: 80px;

    bottom: -30px;

    transform:
      rotate(-8deg);

  }


  /* =========================================================
   FLOATING DOTS
========================================================= */

  .boat-dot {

    position: absolute;

    width: 7px;

    height: 7px;

    background: #5caab2;

    border-radius: 50%;

    opacity: .5;

  }


  .boat-dot-one {

    left: 48%;

    top: 22%;

  }


  .boat-dot-two {

    right: 18%;

    top: 18%;

  }


  .boat-dot-three {

    right: 9%;

    bottom: 20%;

  }


  /* =========================================================
   LEFT CONTENT
========================================================= */

  .boat-copy {

    position: relative;

    z-index: 10;

    padding-left: 10vw;

    max-width: 620px;

  }


  /* =========================================================
   EYEBROW
========================================================= */

  .boat-eyebrow {

    display: inline-flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 25px;

    padding: 9px 17px;

    border: 1px dashed #a9c6c8;

    border-radius: 50px;

    color: #5d7779;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 12px;

    letter-spacing: 1.5px;

  }


  .boat-eyebrow span {

    color: #4d9ca5;

    font-size: 15px;

  }


  /* =========================================================
   TITLE
========================================================= */

  .boat-title {

    margin: 0;

    color: #111;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size:
      clamp(65px, 7vw, 105px);

    line-height: .82;

    font-weight: 800;

    letter-spacing: -7px;

  }


  .boat-title em {

    display: block;

    margin-top: 8px;

    font-family:
      Georgia,
      "Times New Roman",
      serif;

    font-weight: 500;

    font-style: italic;

    letter-spacing: -6px;

  }


  /* =========================================================
   DESCRIPTION
========================================================= */

  .boat-description {

    max-width: 430px;

    margin:
      32px 0 32px;

    color: #607275;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 17px;

    line-height: 1.5;

  }


  /* =========================================================
   FEATURES
========================================================= */

  .boat-features {

    display: flex;

    flex-direction: column;

    gap: 14px;

  }


  .boat-feature {

    display: flex;

    align-items: center;

    gap: 16px;

  }


  .boat-number {

    width: 37px;

    height: 37px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 1px solid #b8d0d1;

    border-radius: 50%;

    color: #698082;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 10px;

  }


  .boat-feature div {

    display: flex;

    flex-direction: column;

    gap: 3px;

  }


  .boat-feature strong {

    color: #111;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 14px;

  }


  .boat-feature small {

    color: #789092;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 11px;

  }


  /* =========================================================
   BOOK BUTTON
========================================================= */

  .boat-book-button {

    width: fit-content;

    display: inline-flex;

    align-items: center;

    gap: 14px;

    margin-top: 32px;

    padding:
      6px 7px 6px 25px;

    background: #111;

    color: #fff;

    text-decoration: none;

    border-radius: 50px;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 14px;

    font-weight: 600;

    transition:
      transform .35s ease,
      box-shadow .35s ease;

  }


  .boat-book-button b {

    width: 45px;

    height: 45px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #fff;

    color: #111;

    border-radius: 50%;

    font-size: 21px;

    font-weight: 400;

    transition:
      transform .35s ease;

  }


  .boat-book-button:hover {

    transform:
      translateY(-4px);

    box-shadow:
      0 18px 35px rgba(0, 0, 0, .14);

  }


  .boat-book-button:hover b {

    transform:
      rotate(45deg);

  }


  /* =========================================================
   RIGHT VISUAL
========================================================= */

  .boat-visual {

    position: relative;

    width: 100%;

    height: 680px;

  }


  /* =========================================================
   WATER CIRCLE
========================================================= */

  .boat-water-circle {

    position: absolute;

    width: 590px;

    height: 590px;

    right: 8%;

    top: 45px;

    border-radius: 50%;

    background: #b9e1e5;

    opacity: .8;

  }


  /* =========================================================
   BOAT IMAGE
========================================================= */

  .boat-image {

    position: absolute;

    z-index: 5;

    width: 650px;

    height: 430px;

    right: 3%;

    top: 105px;

    overflow: hidden;

    border-radius:
      45% 18% 45% 18%;

    box-shadow:
      0 35px 65px rgba(30, 75, 80, .20);

    transform:
      rotate(-3deg);

    animation:
      boatFloat 5s ease-in-out infinite;

  }


  .boat-image img {

    width: 100%;

    height: 100%;

    display: block;

    object-fit: cover;

  }


  /* =========================================================
   BOAT IMAGE SHINE
========================================================= */

  .boat-image::after {

    content: "";

    position: absolute;

    inset: 0;

    background:
      linear-gradient(135deg,
        rgba(255, 255, 255, .18),
        transparent 45%);

    pointer-events: none;

  }


  /* =========================================================
   FLOATING DESTINATION
========================================================= */

  .boat-destination {

    position: absolute;

    z-index: 9;

    left: 7%;

    top: 18%;

    display: flex;

    align-items: center;

    gap: 10px;

    padding:
      11px 17px;

    background: #fff;

    border-radius: 50px;

    box-shadow:
      0 12px 30px rgba(0, 0, 0, .08);

  }


  .boat-destination>span {

    width: 29px;

    height: 29px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #dff4f5;

    color: #398b94;

  }


  .boat-destination div {

    display: flex;

    flex-direction: column;

    gap: 2px;

  }


  .boat-destination strong {

    color: #222;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 11px;

  }


  .boat-destination small {

    color: #999;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 8px;

  }


  /* =========================================================
   BOOKING CARD
========================================================= */

  .boat-book-card {

    position: absolute;

    z-index: 10;

    right: 4%;

    bottom: 75px;

    width: 255px;

    padding: 20px;

    box-sizing: border-box;

    background: #fff;

    border-radius: 24px;

    box-shadow:
      0 22px 45px rgba(0, 0, 0, .14);

  }


  .boat-card-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

    color: #8b9697;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 8px;

    letter-spacing: 1px;

  }


  .boat-card-top i {

    width: 8px;

    height: 8px;

    background: #55aeb0;

    border-radius: 50%;

    box-shadow:
      0 0 0 5px rgba(85, 174, 176, .13);

  }


  .boat-card-title {

    display: block;

    margin-top: 18px;

    color: #111;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 22px;

  }


  .boat-book-card p {

    margin: 6px 0 0;

    color: #999;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 10px;

  }


  .boat-card-bottom {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 20px;

    padding-top: 14px;

    border-top:
      1px solid #eee;

  }


  .boat-card-bottom span {

    color: #999;

    font-size: 9px;

  }


  .boat-card-bottom strong {

    color: #111;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 18px;

  }


  /* =========================================================
   WAVE BADGE
========================================================= */

  .boat-wave-badge {

    position: absolute;

    z-index: 11;

    left: 4%;

    bottom: 22%;

    display: flex;

    align-items: center;

    gap: 9px;

    padding:
      10px 15px;

    background: #fff;

    border-radius: 50px;

    box-shadow:
      0 12px 28px rgba(0, 0, 0, .08);

  }


  .boat-wave-badge>span {

    width: 31px;

    height: 31px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #d7f0f2;

    color: #398e96;

    font-size: 20px;

  }


  .boat-wave-badge div {

    display: flex;

    flex-direction: column;

    gap: 2px;

  }


  .boat-wave-badge strong {

    color: #222;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 10px;

  }


  .boat-wave-badge small {

    color: #999;

    font-size: 8px;

  }


  /* =========================================================
   FLOAT ANIMATION
========================================================= */

  @keyframes boatFloat {

    0%,
    100% {

      transform:
        translateY(0) rotate(-3deg);

    }

    50% {

      transform:
        translateY(-10px) rotate(-3deg);

    }

  }


  /* =========================================================
   BOTTOM LABEL
========================================================= */

  .boat-bottom-label {

    position: absolute;

    left: 10vw;

    bottom: 25px;

    color: #91a5a6;

    font-family:
      Arial,
      Helvetica,
      sans-serif;

    font-size: 9px;

    letter-spacing: 2px;

  }


  .boat-bottom-label span {

    color: #4e9ca4;

  }


  /* =========================================================
   POPUP MODAL (SMOOTH OPEN)
========================================================= */

  .boat-booking-modal {
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
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
  }

  .boat-booking-modal.open {
    opacity: 1;
    visibility: visible;
  }

  .boat-modal-content {
    background: #fff;
    width: 90%;
    max-width: 420px;
    padding: 30px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    font-family: Arial, Helvetica, sans-serif;
    transform: scale(0.9) translateY(20px);
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  .boat-booking-modal.open .boat-modal-content {
    transform: scale(1) translateY(0);
  }

  .boat-modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: transparent;
    border: 0;
    font-size: 28px;
    cursor: pointer;
    color: #999;
    transition: color 0.3s;
  }

  .boat-modal-close:hover {
    color: #333;
  }

  .boat-modal-content h3 {
    margin: 0 0 10px;
    color: #111;
    font-size: 24px;
  }

  .boat-modal-content p {
    margin: 0 0 20px;
    color: #666;
    font-size: 14px;
  }

  .boat-form-group {
    margin-bottom: 15px;
  }

  .boat-form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 12px;
    font-weight: 600;
    color: #333;
  }

  .boat-form-group input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.3s;
  }

  .boat-form-group input:focus {
    border-color: #4d9ca5;
  }

  .boat-submit-btn {
    width: 100%;
    padding: 14px;
    background: #111;
    color: #fff;
    border: 0;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.3s;
  }

  .boat-submit-btn:hover {
    background: #333;
  }

  .boat-form-message {
    margin-top: 15px;
    font-size: 13px;
    text-align: center;
    display: none;
  }

  .boat-form-message.success {
    color: #28a745;
    display: block;
  }

  .boat-form-message.error {
    color: #dc3545;
    display: block;
  }


  /* =========================================================
   TABLET
========================================================= */

  @media (max-width: 1050px) {

    .boat-booking-section {

      grid-template-columns:
        45% 55%;

    }


    .boat-copy {

      padding-left: 6vw;

    }


    .boat-title {

      font-size: 68px;

    }


    .boat-water-circle {

      width: 470px;

      height: 470px;

      right: 0;

    }


    .boat-image {

      width: 530px;

      height: 350px;

      right: -5%;

    }


    .boat-book-card {

      right: 0;

    }

  }


  /* =========================================================
   MOBILE
========================================================= */

  @media (max-width: 700px) {

    .boat-booking-section {

      width: 100%;

      min-height: 900px;

      display: flex;

      flex-direction: column;

      align-items: stretch;

    }


    .boat-copy {

      width: 100%;

      max-width: none;

      padding:
        48px 18px 0;

      box-sizing: border-box;

    }


    .boat-eyebrow {

      margin-bottom: 18px;

      padding:
        7px 12px;

      font-size: 9px;

    }


    .boat-title {

      font-size:
        clamp(52px, 15vw, 72px);

      line-height: .87;

      letter-spacing: -4px;

    }


    .boat-title em {

      letter-spacing: -3px;

    }


    .boat-description {

      max-width: 315px;

      margin:
        21px 0 23px;

      font-size: 14px;

    }


    .boat-features {

      gap: 9px;

    }


    .boat-feature {

      gap: 11px;

    }


    .boat-number {

      width: 30px;

      height: 30px;

      font-size: 9px;

    }


    .boat-feature strong {

      font-size: 12px;

    }


    .boat-feature small {

      font-size: 9px;

    }


    .boat-book-button {

      margin-top: 22px;

      padding:
        5px 6px 5px 20px;

      font-size: 12px;

    }


    .boat-book-button b {

      width: 40px;

      height: 40px;

      font-size: 18px;

    }


    /* VISUAL */

    .boat-visual {

      width: 100%;

      height: 400px;

      margin-top: 18px;

      overflow: hidden;

    }


    .boat-water-circle {

      width: 370px;

      height: 370px;

      right: -115px;

      top: 5px;

    }


    .boat-image {

      width: 430px;

      height: 280px;

      right: -85px;

      top: 48px;

      border-radius:
        42% 14% 40% 14%;

    }


    .boat-destination {

      left: 15px;

      top: 18px;

      padding:
        8px 12px;

    }


    .boat-destination>span {

      width: 26px;

      height: 26px;

    }


    .boat-destination strong {

      font-size: 9px;

    }


    .boat-destination small {

      font-size: 7px;

    }


    .boat-book-card {

      width: 190px;

      right: 15px;

      bottom: 22px;

      padding: 15px;

      border-radius: 18px;

    }


    .boat-card-title {

      margin-top: 12px;

      font-size: 17px;

    }


    .boat-book-card p {

      font-size: 8px;

    }


    .boat-card-bottom {

      margin-top: 13px;

      padding-top: 10px;

    }


    .boat-wave-badge {

      left: 15px;

      bottom: 27px;

      padding:
        7px 10px;

    }


    .boat-wave-badge>span {

      width: 27px;

      height: 27px;

      font-size: 17px;

    }


    .boat-wave-badge strong {

      font-size: 8px;

    }


    .boat-wave-badge small {

      font-size: 7px;

    }


    .boat-bottom-label {

      left: 18px;

      bottom: 8px;

      font-size: 7px;

    }


    .boat-wave {

      display: none;

    }

  }


  /* =========================================================
   SMALL MOBILE
========================================================= */

  @media (max-width: 390px) {

    .boat-booking-section {

      min-height: 850px;

    }


    .boat-copy {

      padding:
        42px 16px 0;

    }


    .boat-title {

      font-size: 50px;

    }


    .boat-description {

      font-size: 13px;

      max-width: 285px;

    }


    .boat-visual {

      height: 350px;

    }


    .boat-water-circle {

      width: 330px;

      height: 330px;

      right: -110px;

    }


    .boat-image {

      width: 395px;

      height: 255px;

      right: -90px;

      top: 45px;

    }


    .boat-book-card {

      width: 175px;

      right: 12px;

      bottom: 18px;

    }


    .boat-wave-badge {

      left: 12px;

      bottom: 20px;

    }


    .boat-bottom-label {

      display: none;

    }

  }
</style>


<script>
  document.addEventListener("DOMContentLoaded", function() {

    const modal = document.getElementById('boatBookingModal');
    const closeModal = document.getElementById('closeBoatModal');
    const bookBtn = document.querySelector('.boat-book-button');
    const form = document.getElementById('boatBookingForm');
    const formMessage = document.getElementById('boatFormMessage');

    /* OPEN MODAL SMOOTHLY */
    if (bookBtn) {
      bookBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.add('open');
        formMessage.style.display = 'none';
      });
    }

    /* CLOSE MODAL */
    if (closeModal) {
      closeModal.addEventListener('click', function() {
        modal.classList.remove('open');
      });
    }

    /* CLOSE ON OUTSIDE CLICK */
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.classList.remove('open');
      }
    });

    /* FORM SUBMISSION */
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const name = document.getElementById('boatCustomerName').value;
      const mobile = document.getElementById('boatMobile').value;

      // Simple validation
      if (!name || !mobile) {
        formMessage.textContent = 'Please fill in all fields.';
        formMessage.className = 'boat-form-message error';
        return;
      }

      /* SEND AJAX REQUEST (No vehicle_id needed) */
      const formData = new FormData();
      formData.append('customer_name', name);
      formData.append('mobile', mobile);

      fetch('ajax/submit_boat_booking.php', { // CHANGE THIS to your PHP file path
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            formMessage.textContent = data.message;
            formMessage.className = 'boat-form-message success';
            form.reset();
            setTimeout(() => {
              modal.classList.remove('open');
            }, 2000);
          } else {
            formMessage.textContent = data.message;
            formMessage.className = 'boat-form-message error';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          formMessage.textContent = 'Something went wrong. Please try again.';
          formMessage.className = 'boat-form-message error';
        });
    });

    /* HOVER EFFECT FOR ARROW */
    const arrow = document.querySelector('.boat-book-button b');
    if (arrow) {
      bookBtn.addEventListener('mouseenter', () => {
        arrow.style.transform = 'rotate(45deg)';
      });
      bookBtn.addEventListener('mouseleave', () => {
        arrow.style.transform = 'rotate(0deg)';
      });
    }
  });
</script>