<section class="travel-hero-section">

  <!-- TRAVEL PATH -->
  <svg class="travel-flight-path" viewBox="0 0 1000 500" aria-hidden="true">
    <path
      d="M40 120
         C260 100 430 170 360 280
         C300 380 600 470 900 330" />
  </svg>

  <!-- AIRPLANE -->
  <div class="travel-plane">
    ✈
  </div>

  <!-- MAIN CONTENT -->
  <div class="travel-hero-content">

    <h1 class="travel-hero-title">

      <span>The Whole</span>

      <span class="travel-world-line">
        W
        <span class="travel-world">
          <img
            src="https://framerusercontent.com/images/619b3KdOinvkQ74s5Mg2BjtTGY8.png?width=100&height=100"
            alt="World">
        </span>
        rld
      </span>

      <span>
        is <i>Waiting</i> For
      </span>

      <span>You</span>

    </h1>

    <!-- BUTTON -->
    <a href="#" class="travel-hero-button">
      <span>Start Planning</span>
      <b>↗</b>
    </a>

  </div>

  <!-- PHOTO STACK -->
  <div class="travel-photo-stack">

    <div class="travel-photo travel-photo-1">
      <img
        src="https://images.unsplash.com/photo-1564399579883-451a5d44ec08?auto=format&fit=crop&w=700&q=90"
        alt="Travel destination">
    </div>

    <div class="travel-photo travel-photo-2">
      <img
        src="https://images.unsplash.com/photo-1508804185872-d7badad00f7d?auto=format&fit=crop&w=700&q=90"
        alt="Travel destination">
    </div>

    <div class="travel-photo travel-photo-3">
      <img
        src="https://images.unsplash.com/photo-1548919973-5cef591cdbc9?auto=format&fit=crop&w=700&q=90"
        alt="Travel destination">
    </div>

  </div>

  <!-- BRAZIL TICKET -->
  <div class="travel-ticket">

    <small>RIO DE JANEIRO</small>

    <strong>BRAZIL</strong>

    <span>EXPLORE THE WORLD</span>

  </div>

  <!-- LOCATION -->
  <div class="travel-location-pin">
    📍
  </div>

</section>

<style>
  /* =========================================================
     TRAVEL HERO SECTION
     All classes prefixed with "travel-" to avoid conflicts
  ========================================================= */

  .travel-hero-section {
    position: relative;
    width: 100%;
    min-height: 800px;
    overflow: hidden;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* =========================================================
     MAIN CONTENT
  ========================================================= */

  .travel-hero-content {
    position: relative;
    z-index: 20;
    width: min(900px, 90%);
    text-align: center;
    margin-top: -15px;
  }

  /* =========================================================
     TITLE
  ========================================================= */

  .travel-hero-title {
    margin: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #111111;
    font-family: Arial, Helvetica, sans-serif;
    font-size: clamp(70px, 7vw, 130px);
    line-height: .98;
    font-weight: 800;
    letter-spacing: -6px;
  }

  .travel-hero-title > span {
    display: block;
  }

  .travel-hero-title i {
    font-family: Georgia, "Times New Roman", serif;
    font-weight: 500;
    font-style: italic;
    letter-spacing: -7px;
  }

  /* =========================================================
     WORLD LINE
  ========================================================= */

  .travel-world-line {
    display: flex !important;
    flex-direction: row !important;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
  }

  /* =========================================================
     WORLD IMAGE
  ========================================================= */

  .travel-world {
    display: inline-flex;
    flex-shrink: 0;
    width: 78px;
    height: 78px;
    margin: 0 8px;
    align-items: center;
    justify-content: center;
    vertical-align: middle;
  }

  .travel-world img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: contain;
  }

  /* =========================================================
     BUTTON
  ========================================================= */

  .travel-hero-button {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 22px;
    margin-top: 12px;
    padding: 5px 7px 5px 30px;
    background: #ffd5e2;
    color: #111111;
    text-decoration: none;
    border-radius: 60px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 21px;
    font-weight: 700;
    transform: rotate(2deg);
    transition: transform .3s ease, box-shadow .3s ease;
  }

  .travel-hero-button:hover {
    transform: rotate(0deg) scale(1.03);
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
  }

  .travel-hero-button b {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border-radius: 50%;
    font-size: 31px;
    font-weight: 400;
  }

  /* =========================================================
     AIRPLANE
  ========================================================= */

  .travel-plane {
    position: absolute;
    z-index: 8;
    left: 18%;
    top: 25%;
    font-size: 52px;
    color: #dfe4e8;
    transform: rotate(8deg);
    filter: drop-shadow(0 5px 5px rgba(0, 0, 0, .08));
    pointer-events: none;
  }

  /* =========================================================
     FLIGHT PATH
  ========================================================= */

  .travel-flight-path {
    position: absolute;
    z-index: 2;
    width: 65%;
    height: 60%;
    left: 18%;
    top: 24%;
    overflow: visible;
    pointer-events: none;
  }

  .travel-flight-path path {
    fill: none;
    stroke: #dddddd;
    stroke-width: 2;
    stroke-dasharray: 4 7;
    stroke-linecap: round;
  }

  /* =========================================================
     PHOTO STACK
  ========================================================= */

  .travel-photo-stack {
    position: absolute;
    z-index: 12;
    right: 7%;
    top: 3%;
    width: 300px;
    height: 350px;
    pointer-events: none;
  }

  /* =========================================================
     PHOTO
  ========================================================= */

  .travel-photo {
    position: absolute;
    width: 260px;
    height: 220px;
    overflow: hidden;
    border-radius: 22px;
    border: 8px solid #ffffff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, .04);
  }

  .travel-photo img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
  }

  .travel-photo-1 {
    right: 0;
    top: 0;
    transform: rotate(-6deg);
    border-color: #c9e9ff;
  }

  .travel-photo-2 {
    right: 45px;
    top: 70px;
    transform: rotate(5deg);
    border-color: #ffd4c8;
  }

  .travel-photo-3 {
    right: 65px;
    top: 110px;
    transform: rotate(7deg);
    border-color: #ffdce9;
  }

  /* =========================================================
     BRAZIL TICKET
  ========================================================= */

  .travel-ticket {
    position: absolute;
    z-index: 12;
    left: 17%;
    bottom: 18%;
    width: 160px;
    height: 95px;
    padding: 12px;
    box-sizing: border-box;
    border: 3px dotted #9ac65c;
    background: #ffffff;
    transform: rotate(-30deg);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .travel-ticket small {
    font-size: 11px;
    color: #777777;
  }

  .travel-ticket strong {
    font-size: 24px;
    font-weight: 500;
    color: #ff8b43;
  }

  .travel-ticket span {
    font-size: 8px;
    color: #777777;
  }

  /* =========================================================
     LOCATION PIN
  ========================================================= */

  .travel-location-pin {
    position: absolute;
    z-index: 12;
    right: 20%;
    bottom: 28%;
    font-size: 35px;
    transform: rotate(12deg);
  }

  /* =========================================================
     TABLET
  ========================================================= */

  @media (max-width: 1000px) {
    .travel-hero-section {
      min-height: 720px;
    }

    .travel-hero-title {
      font-size: clamp(62px, 9vw, 90px);
      letter-spacing: -5px;
    }

    .travel-world {
      width: 62px;
      height: 62px;
      margin: 0 6px;
    }

    .travel-photo-stack {
      right: -40px;
      top: 2%;
      transform: scale(.72);
      transform-origin: top right;
    }

    .travel-plane {
      left: 8%;
      top: 28%;
    }

    .travel-ticket {
      left: 7%;
      bottom: 12%;
    }
  }

  /* =========================================================
     MOBILE
  ========================================================= */

  @media (max-width: 600px) {
    .travel-hero-section {
      min-height: 680px;
      display: block;
    }

    .travel-hero-content {
      position: absolute;
      z-index: 30;
      top: 180px;
      left: 50%;
      width: 94%;
      margin: 0;
      transform: translateX(-50%);
    }

    .travel-hero-title {
      font-size: clamp(46px, 13.5vw, 64px);
      line-height: .98;
      letter-spacing: -3px;
    }

    .travel-hero-title i {
      letter-spacing: -3px;
    }

    .travel-world {
      width: 58px;
      height: 58px;
      margin: 0 5px;
    }

    .travel-photo-stack {
      z-index: 10;
      top: -20px;
      right: -85px;
      width: 220px;
      height: 260px;
      transform: scale(.55);
      transform-origin: top right;
    }

    .travel-plane {
      z-index: 5;
      left: 4%;
      top: 25%;
      font-size: 28px;
    }

    .travel-flight-path {
      z-index: 2;
      width: 130%;
      height: 55%;
      left: -18%;
      top: 29%;
    }

    .travel-hero-button {
      margin-top: 12px;
      gap: 12px;
      padding: 4px 5px 4px 20px;
      font-size: 15px;
    }

    .travel-hero-button b {
      width: 43px;
      height: 43px;
      font-size: 24px;
    }

    .travel-ticket {
      left: 8%;
      bottom: 12%;
      width: 135px;
      height: 82px;
      transform: rotate(-27deg);
    }

    .travel-ticket small {
      font-size: 8px;
    }

    .travel-ticket strong {
      font-size: 20px;
    }

    .travel-ticket span {
      font-size: 6px;
    }

    .travel-location-pin {
      right: 9%;
      bottom: 21%;
      font-size: 27px;
    }
  }

  /* =========================================================
     SMALL PHONES
  ========================================================= */
</style>

<script>
  document.addEventListener("DOMContentLoaded", () => {

    const plane = document.querySelector(".travel-plane");
    const photos = document.querySelectorAll(".travel-photo");
    const ticket = document.querySelector(".travel-ticket");

    /* =========================================
       PHOTO FLOAT
    ========================================= */

    photos.forEach((photo, index) => {
      const rotation = index === 0 ? -6 : index === 1 ? 5 : 7;

      photo.animate(
        [{
          transform: `translateY(0) rotate(${rotation}deg)`
        },
        {
          transform: `translateY(-7px) rotate(${rotation}deg)`
        },
        {
          transform: `translateY(0) rotate(${rotation}deg)`
        }
        ],
        {
          duration: 4200 + index * 500,
          iterations: Infinity,
          easing: "ease-in-out"
        }
      );
    });

    /* =========================================
       AIRPLANE FLOAT
    ========================================= */

    if (plane) {
      plane.animate(
        [{
          transform: "translate(0,0) rotate(8deg)"
        },
        {
          transform: "translate(7px,-4px) rotate(10deg)"
        },
        {
          transform: "translate(0,0) rotate(8deg)"
        }
        ],
        {
          duration: 3500,
          iterations: Infinity,
          easing: "ease-in-out"
        }
      );
    }

    /* =========================================
       TICKET FLOAT
    ========================================= */

    if (ticket) {
      ticket.animate(
        [{
          transform: "rotate(-30deg)"
        },
        {
          transform: "translateY(-5px) rotate(-28deg)"
        },
        {
          transform: "rotate(-30deg)"
        }
        ],
        {
          duration: 4000,
          iterations: Infinity,
          easing: "ease-in-out"
        }
      );
    }

  });
</script>