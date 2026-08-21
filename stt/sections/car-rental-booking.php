<section class="rental-section">

  <!-- DECORATIVE BACKGROUND -->
  <div class="rental-route"></div>


  <!-- =========================
       LEFT CONTENT
  ========================== -->

  <div class="rental-copy">

    <div class="rental-eyebrow">
      <span>✦</span>
      YOUR JOURNEY, YOUR WAY
    </div>


    <h2 class="rental-title">
      Travel
      <em>Your Way</em>
    </h2>


    <p class="rental-description">
      Comfortable vehicles, flexible routes and packages
      designed for your journey.
    </p>


    <!-- FEATURES -->

    <div class="rental-features">

      <div class="rental-feature">

        <span class="rental-number">
          01
        </span>

        <div>
          <strong>Comfortable Vehicles</strong>
          <small>Cars for every kind of journey.</small>
        </div>

      </div>


      <div class="rental-feature">

        <span class="rental-number">
          02
        </span>

        <div>
          <strong>Flexible Routes</strong>
          <small>Travel wherever your plans take you.</small>
        </div>

      </div>


      <div class="rental-feature">

        <span class="rental-number">
          03
        </span>

        <div>
          <strong>Custom Packages</strong>
          <small>Built around your schedule.</small>
        </div>

      </div>

    </div>


    <!-- BOOK BUTTON -->

    <a href="<?= SITE_URL; ?>car-rental-booking.php" class="rental-book-button">

      <span>
        Book a Car
      </span>

      <b>
        ↗
      </b>

    </a>

  </div>



  <!-- =========================
       RIGHT CAR VISUAL
  ========================== -->

  <div class="rental-visual">

    <!-- BACK CIRCLE -->

    <div class="rental-circle"></div>


    <!-- CAR -->

    <div class="rental-car">

      <img
        src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=90"
        alt="Premium rental car"
      >

    </div>


    <!-- FLOATING LABEL -->

    <div class="rental-floating-label">

      <span>✦</span>

      READY TO DRIVE

    </div>

  </div>



  <!-- =========================
       BOTTOM TEXT
  ========================== -->

  <div class="rental-bottom-label">

    <span>✦</span>

    DRIVE • EXPLORE • DISCOVER

  </div>

</section>



<style>

/* =========================================================
   RENTAL SECTION
========================================================= */

.rental-section {

  position: relative;

  width: 100%;

  min-height: 820px;

  overflow: hidden;

  background: #f7f3ea;

  display: grid;

  grid-template-columns: 42% 58%;

  align-items: center;

  box-sizing: border-box;

}


/* =========================================================
   BACKGROUND ROUTE
========================================================= */

.rental-route {

  position: absolute;

  width: 60%;

  height: 350px;

  right: -8%;

  top: 48%;

  border-top: 1px dashed #d5cec1;

  border-radius: 50%;

  transform:
    translateY(-50%)
    rotate(-8deg);

  opacity: .45;

  pointer-events: none;

}


/* =========================================================
   LEFT CONTENT
========================================================= */

.rental-copy {

  position: relative;

  z-index: 10;

  padding-left: 10vw;

  max-width: 620px;

}


/* =========================================================
   SMALL LABEL
========================================================= */

.rental-eyebrow {

  display: inline-flex;

  align-items: center;

  gap: 10px;

  margin-bottom: 25px;

  padding: 9px 16px;

  border: 1px dashed #cfc8ba;

  border-radius: 50px;

  color: #68655f;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 13px;

  letter-spacing: 1px;

}


.rental-eyebrow span {

  color: #ff8065;

  font-size: 16px;

}


/* =========================================================
   MAIN TITLE
========================================================= */

.rental-title {

  margin: 0;

  color: #111;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size:
    clamp(60px, 6vw, 92px);

  line-height: .9;

  font-weight: 800;

  letter-spacing: -6px;

}


.rental-title em {

  display: block;

  margin-top: 7px;

  font-family:
    Georgia,
    "Times New Roman",
    serif;

  font-weight: 500;

  font-style: italic;

  letter-spacing: -5px;

}


/* =========================================================
   DESCRIPTION
========================================================= */

.rental-description {

  max-width: 450px;

  margin:
    32px 0 35px;

  color: #68655f;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 19px;

  line-height: 1.5;

}


/* =========================================================
   FEATURES
========================================================= */

.rental-features {

  display: flex;

  flex-direction: column;

  gap: 14px;

}


.rental-feature {

  display: flex;

  align-items: center;

  gap: 17px;

}


/* NUMBER CIRCLE */

.rental-number {

  width: 36px;

  height: 36px;

  flex-shrink: 0;

  display: flex;

  align-items: center;

  justify-content: center;

  border:
    1px solid #d2ccbf;

  border-radius: 50%;

  color: #777;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 11px;

}


.rental-feature div {

  display: flex;

  flex-direction: column;

  gap: 3px;

}


.rental-feature strong {

  color: #111;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 15px;

}


.rental-feature small {

  color: #88837a;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 12px;

}


/* =========================================================
   BOOK BUTTON
========================================================= */

.rental-book-button {

  width: fit-content;

  display: inline-flex;

  align-items: center;

  gap: 14px;

  margin-top: 35px;

  padding:
    6px 7px 6px 24px;

  background: #111;

  color: #fff;

  text-decoration: none;

  border-radius: 50px;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 15px;

  font-weight: 600;

  transition:
    transform .35s ease,
    box-shadow .35s ease;

}


.rental-book-button b {

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


.rental-book-button:hover {

  transform:
    translateY(-4px);

  box-shadow:
    0 15px 30px
    rgba(0,0,0,.15);

}


.rental-book-button:hover b {

  transform:
    rotate(45deg);

}


/* =========================================================
   RIGHT VISUAL
========================================================= */

.rental-visual {

  position: relative;

  width: 100%;

  height: 720px;

}


/* =========================================================
   BACKGROUND CIRCLE
========================================================= */

.rental-circle {

  position: absolute;

  width: 580px;

  height: 580px;

  right: 8%;

  top: 55px;

  border-radius: 50%;

  background: #e9dfcf;

}


/* =========================================================
   CAR IMAGE
========================================================= */

.rental-car {

  position: absolute;

  z-index: 5;

  width: 650px;

  height: 420px;

  right: 2%;

  top: 105px;

  overflow: hidden;

  border-radius:
    45% 18% 40% 18%;

  box-shadow:
    0 35px 60px
    rgba(40,35,25,.15);

  animation:
    rentalCarFloat
    5s
    ease-in-out
    infinite;

}


.rental-car img {

  width: 100%;

  height: 100%;

  display: block;

  object-fit: cover;

}


/* =========================================================
   FLOATING LABEL
========================================================= */

.rental-floating-label {

  position: absolute;

  z-index: 8;

  left: 15%;

  bottom: 18%;

  display: flex;

  align-items: center;

  gap: 8px;

  padding:
    9px 14px;

  background: #fff;

  border-radius: 50px;

  box-shadow:
    0 10px 25px
    rgba(0,0,0,.08);

  color: #777;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 10px;

  letter-spacing: 1px;

}


.rental-floating-label span {

  color: #ff8065;

}


/* =========================================================
   CAR FLOAT ANIMATION
========================================================= */

@keyframes rentalCarFloat {

  0%,
  100% {

    transform:
      translateY(0)
      rotate(-3deg);

  }

  50% {

    transform:
      translateY(-9px)
      rotate(-3deg);

  }

}


/* =========================================================
   BOTTOM LABEL
========================================================= */

.rental-bottom-label {

  position: absolute;

  z-index: 10;

  left: 10vw;

  bottom: 25px;

  color: #aaa;

  font-family:
    Arial,
    Helvetica,
    sans-serif;

  font-size: 10px;

  letter-spacing: 2px;

}


.rental-bottom-label span {

  color: #ff8065;

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 1050px) {

  .rental-section {

    grid-template-columns:
      45% 55%;

  }


  .rental-copy {

    padding-left: 6vw;

  }


  .rental-title {

    font-size: 65px;

  }


  .rental-circle {

    width: 470px;

    height: 470px;

    right: 0;

  }


  .rental-car {

    width: 530px;

    height: 350px;

    right: -5%;

  }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 700px) {

  .rental-section {

    width: 100%;

    min-height: 900px;

    display: flex;

    flex-direction: column;

    align-items: stretch;

    overflow: hidden;

  }


  /* LEFT */

  .rental-copy {

    width: 100%;

    max-width: none;

    padding:
      50px 18px 0;

    box-sizing: border-box;

  }


  .rental-eyebrow {

    margin-bottom: 18px;

    padding:
      7px 12px;

    font-size: 9px;

  }


  .rental-eyebrow span {

    font-size: 12px;

  }


  .rental-title {

    font-size:
      clamp(50px, 15vw, 70px);

    line-height: .9;

    letter-spacing: -4px;

  }


  .rental-title em {

    margin-top: 4px;

    letter-spacing: -3px;

  }


  .rental-description {

    max-width: 310px;

    margin:
      20px 0 24px;

    font-size: 14px;

    line-height: 1.45;

  }


  /* FEATURES */

  .rental-features {

    gap: 9px;

  }


  .rental-feature {

    gap: 11px;

  }


  .rental-number {

    width: 30px;

    height: 30px;

    font-size: 9px;

  }


  .rental-feature strong {

    font-size: 12px;

  }


  .rental-feature small {

    font-size: 9px;

  }


  /* BUTTON */

  .rental-book-button {

    margin-top: 24px;

    padding:
      5px 6px 5px 20px;

    font-size: 13px;

  }


  .rental-book-button b {

    width: 40px;

    height: 40px;

    font-size: 18px;

  }


  /* RIGHT IMAGE AREA */

  .rental-visual {

    width: 100%;

    height: 390px;

    margin-top: 15px;

    overflow: hidden;

  }


  /* BACK CIRCLE */

  .rental-circle {

    width: 360px;

    height: 360px;

    right: -115px;

    top: 5px;

  }


  /* CAR */

  .rental-car {

    width: 430px;

    height: 280px;

    right: -95px;

    top: 50px;

    border-radius:
      45% 15% 40% 15%;

  }


  /* LABEL */

  .rental-floating-label {

    left: 18px;

    bottom: 25px;

    padding:
      8px 12px;

    font-size: 9px;

  }


  /* HIDE ROUTE ON MOBILE */

  .rental-route {

    display: none;

  }


  /* BOTTOM */

  .rental-bottom-label {

    left: 18px;

    bottom: 9px;

    font-size: 8px;

    letter-spacing: 1.3px;

  }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 390px) {

  .rental-section {

    min-height: 850px;

  }


  .rental-copy {

    padding:
      45px 16px 0;

  }


  .rental-title {

    font-size: 50px;

  }


  .rental-description {

    max-width: 285px;

    font-size: 13px;

  }


  .rental-visual {

    height: 350px;

    margin-top: 12px;

  }


  .rental-circle {

    width: 330px;

    height: 330px;

    right: -115px;

  }


  .rental-car {

    width: 395px;

    height: 255px;

    right: -95px;

    top: 45px;

  }


  .rental-floating-label {

    left: 15px;

    bottom: 18px;

  }


  .rental-bottom-label {

    display: none;

  }

}

</style>


<script>

document.addEventListener(
  "DOMContentLoaded",
  function () {

    const button =
      document.querySelector(
        ".rental-book-button"
      );


    if (!button) return;


    const arrow =
      button.querySelector("b");


    button.addEventListener(
      "mouseenter",
      function () {

        arrow.style.transform =
          "rotate(45deg)";

      }
    );


    button.addEventListener(
      "mouseleave",
      function () {

        arrow.style.transform =
          "rotate(0deg)";

      }
    );

  }
);

</script>
